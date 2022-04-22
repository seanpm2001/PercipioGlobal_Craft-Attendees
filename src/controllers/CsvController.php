<?php

namespace percipiolondon\attendees\controllers;

use Craft;

use craft\helpers\ArrayHelper;
use League\Csv\AbstractCsv;
use League\Csv\Exception;
use League\Csv\Reader;
use craft\web\Controller;
use League\Csv\Statement;
use percipiolondon\attendees\Attendees;
use percipiolondon\attendees\jobs\CreateAttendeeJob;
use percipiolondon\attendees\models\Export as ExportModel;
use craft\helpers\Queue;
use yii\web\Response;
use yii\web\UploadedFile;

class CsvController extends Controller
{
    const IMPORT_CSV_FIELDS = [
        'orgName',
        'orgUrn',
        'postCode',
        'name',
        'email',
        'jobRole',
        'days',
        'newsletter',
        'eventId'
    ];

    public function actionExport()
    {
        $this->requireLogin();

        $request = Craft::$app->getRequest();

        $exportModel = new ExportModel();
        $exportModel->exportType = $request->getBodyParam('exportType');
        $exportModel->eventType = $request->getBodyParam('eventType');
        $exportModel->school = $request->getBodyParam('school');
        $exportModel->start = $request->getBodyParam('start');
        $exportModel->end = $request->getBodyParam('end');
        $exportModel->site = $request->getBodyParam('site');
        $exportModel->tag = $request->getBodyParam('tag');

        $exporter = Attendees::getInstance()->export->export($exportModel);

        $filename = match ($exportModel->exportType) {
            'event' => 'export_events_' . date("Y_m_d_H_i") . '.zip',
            'subscriptions' => 'export_subscriptions_' . date("Y_m_d_H_i") . '.zip',
            'school-unique', 'school-attendee' => 'export_schools_' . date("Y_m_d_H_i") . '.zip',
            default => 'export_attendees_' . date("Y_m_d_H_i") . '.zip',
        };

        return $exporter->export()->send($filename);
    }

    public function actionImport(): Response
    {
        $this->requireLogin();
        $request = Craft::$app->getRequest();

        $variables = [];

        $variables['controllerHandle'] = 'file';

        //event
        $event = \craft\elements\Entry::find()
            ->id($request->getBodyParam('event'))
            ->site('*')
            ->anyStatus()
            ->one();

        // The CSV file
        $file = UploadedFile::getInstanceByName('file');
        if ($file !== null) {
            $filename = uniqid($file->name, true);
            $filePath = Craft::$app->getPath()->getTempPath().DIRECTORY_SEPARATOR.$filename;
            $file->saveAs($filePath, false);
            // Also save the file to the cache as a backup way to access it
            $cache = Craft::$app->getCache();
            $fileHandle = fopen($filePath, 'r');
            if ($fileHandle) {
                $fileContents = fgets($fileHandle);
                if ($fileContents) {
                    $cache->set($filePath, $fileContents);
                }
                fclose($fileHandle);
            }
            // Read in the headers
            $csv = Reader::createFromPath($file->tempName);
            try {
//                $csv->setDelimiter(Retour::$settings->csvColumnDelimiter ?? ',');
                $csv->setDelimiter(',');
            } catch (Exception $e) {
                Craft::error($e, __METHOD__);
            }
            $headers = $csv->fetchOne(0);
            Craft::info(print_r($headers, true), __METHOD__);
            $variables['headers'] = $headers;
            $variables['filepath'] = $filePath;
            $variables['filename'] = $file->name;
            $variables['event'] = $event;
        }

        // Render the template
        return $this->renderTemplate('craft-attendees/trainings/import', $variables);
    }

    public function actionImportCsvColumns()
    {
        // If your CSV document was created or is read on a Macintosh computer,
        // add the following lines before using the library to help PHP detect line ending in Mac OS X
        if (!ini_get('auto_detect_line_endings')) {
            ini_set('auto_detect_line_endings', '1');
        }

        $this->requirePostRequest();

        $filename = Craft::$app->getRequest()->getRequiredBodyParam('filename');
        $file = Craft::$app->getRequest()->getRequiredBodyParam('file');
        $columns = Craft::$app->getRequest()->getRequiredBodyParam('columns');
        $eventId = Craft::$app->getRequest()->getRequiredBodyParam('event');
        $siteId = Craft::$app->getRequest()->getRequiredBodyParam('site');
        $headers = null;

        try {
            $csv = Reader::createFromPath($filename);
            $csv->setDelimiter(',');
            $headers = array_flip($csv->fetchOne(0));
        } catch (\Exception $e) {
            // If this throws an exception, try to read the CSV file from the data cache
            // This can happen on load balancer setups where the Craft temp directory isn't shared
            $cache = Craft::$app->getCache();
            $cachedFile = $cache->get($filename);
            if ($cachedFile !== false) {
                $csv = Reader::createFromString($cachedFile);
                try {
                    $csv->setDelimiter(',');
                } catch (Exception $e) {
                    Craft::error($e, __METHOD__);
                }
                $headers = array_flip($csv->fetchOne(0));
                $cache->delete($filename);
            } else {
                Craft::error("Could not import ${$filename} from the file system, or the cache.", __METHOD__);
            }
        }

        // If we have headers, then we have a file, so parse it
        if ($headers !== null) {
            $this->importCsvApi9($csv, $columns, $headers, $eventId, $siteId, $file, $filename);
            @unlink($filename);
            Attendees::$plugin->clearAllCaches();
            Craft::$app->getSession()->setNotice(Craft::t('craft-attendees', 'Imports from CSV started.'));
        } else {
            Craft::$app->getSession()->setError(Craft::t('craft-attendees', 'CSV imports could not be imported.'));
        }

        // Render the template
        $event = \craft\elements\Entry::find()
            ->id($eventId)
            ->siteId($siteId)
            ->anyStatus()
            ->one();

        return $this->renderTemplate('craft-attendees/trainings/detail', [
            'event' => $event,
            'tab' => 'logs'
        ]);
    }

    /**
     * @param AbstractCsv $csv
     * @param array $columns
     * @param array $headers
     * @throws \League\Csv\Exception
     */
    protected function importCsvApi9(AbstractCsv $csv, array $columns, array $headers, string $eventId, string $siteId, string $file, string $filepath)
    {
        $stmt = (new Statement())
            ->offset(1)
        ;
        $rows = $stmt->process($csv);
        $columns = ArrayHelper::filterEmptyStringsFromArray($columns);
        $totalRows = count($csv) - 1;
        $rowCount = 0;
        foreach ($rows as $row) {
            $rowCount++;

            $config = [
                'id' => 0,
                'event' => $eventId,
                'site' => $siteId,
                'file' => $file,
                'filepath' => $filepath,
                'line' => $rowCount,
                'total' => $totalRows
            ];
            $index = 0;
            foreach (self::IMPORT_CSV_FIELDS as $importField) {

                if (isset($columns[$index], $headers[$columns[$index]])) {
                    $config[$importField] = empty($row[$headers[$columns[$index]]])
                        ? null
                        : $row[$headers[$columns[$index]]];
                }
                $index++;
            }

            Queue::push(new CreateAttendeeJob([
                'config' => $config
            ]));
        }

    }

}
