<?php

namespace percipiolondon\attendees\controllers;

use craft\elements\Entry;
use craft\feeds\GuzzleClient;
use craft\web\Controller;

use Craft;
use League\Csv\Exception;
use League\Csv\Reader;
use percipiolondon\attendees\Attendees;
use percipiolondon\attendees\elements\Attendee;
use percipiolondon\attendees\records\Attendee as AttendeeRecord;
use percipiolondon\attendees\helpers\Attendee as AttendeeHelper;
use percipiolondon\attendees\records\FollowOnSupport;
use percipiolondon\attendees\records\FollowOnSupportOptions;
use yii\web\HttpException;
use yii\web\Response;
use yii\web\UploadedFile;

class TrainingController extends Controller
{
    protected $allowAnonymous = ['save', 'delete', 'save-support-options'];

//    public function actionIndex()
//    {
//        return $this->renderTemplate('craft-attendees/dashboard/index', []);
//    }

    public function actionDetail(int $eventId, string $site = '*')
    {
        $event = \craft\elements\Entry::find()
            ->id($eventId)
            ->site($site)
            ->anyStatus()
            ->one();

        return $this->renderTemplate('craft-attendees/trainings/detail', [
            'event' => $event,
        ]);
    }

    public function actionFetchSupportOptions(int $eventId)
    {
        $options = FollowOnSupportOptions::find()->all();
        $selectedOptions = FollowOnSupport::find()->where(['eventId' => $eventId])->all();

        return $this->asJson([
            "options" => $options,
            "selectedOptions" => $selectedOptions
        ]);
    }

    public function actionAttendees(int $eventId, int $limit = 0, int $offset = 0)
    {
        $limit = $limit === 0 ? "*" : $limit;

        $attendees = Attendee::find()
            ->where(['eventId' => $eventId])
            ->orderBy('dateCreated DESC')
            ->offset($offset)
            ->limit($limit)
            ->all();

        $count = Attendee::find()
            ->where(['eventId' => $eventId])
            ->count();

        return $this->asJson([
            "meta" => [
                "event" => $eventId,
                "offset" => $offset,
                "limit" => $limit,
                "total" => $count
            ],
            "attendees" => $attendees
        ]);
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

    public function actionSave()
    {
        $this->requireLogin();
        $this->requireAcceptsJson();
        $request = Craft::$app->getRequest();
        $attendee = AttendeeHelper::populateAttendeeFromPost($request);

        $success = Craft::$app->getElements()->saveElement($attendee);
        $savedAttendee = Attendee::find()
            ->where(['eventId' => $attendee->eventId, 'name' => $attendee->name, 'orgName' => $attendee->orgName, 'email' => $attendee->email])
            ->one();

        $response = (object)[
            "success" => $success,
            "errors" => $attendee->getErrors(),
            "attendee" => $savedAttendee
        ];

        return $this->asJson($response);
    }

    public function actionDelete()
    {
        $this->requireLogin();
        $this->requireAcceptsJson();

        $attendeeId = Craft::$app->getRequest()->getRequiredBodyParam('attendeeId');
        $attendee = Attendee::find()->id($attendeeId)->one();

        if(!$attendee){
            throw new HttpException(404, Craft::t('craft-attendees', 'Can not find attendee.'));
        }

        if (!Craft::$app->getElements()->deleteElementById($attendee->id)) {
            return $this->asJson(['success' => false]);
        }

        return $this->asJson(['success' => true, 'attendee' => $attendee]);
    }

    public function actionSaveSupportOptions()
    {
        $this->requireLogin();
        $this->requireAcceptsJson();
        $request = Craft::$app->getRequest();

        $value = $request->getBodyParam('value');
        $event = $request->getBodyParam('event');

        $option = FollowOnSupportOptions::find()->where(['value' => $value])->one();

        if($option){
            $entry = FollowOnSupport::find()->where(['optionId' => $option->id, 'eventId' => $event])->one();

            if(!$entry){
                //add
                $entry = new FollowOnSupport();

                $entry->optionId = $option->id;
                $entry->eventId = $event;

                $entry->save();

            }else{
                //remove
                if(!$entry->delete()){
                    return $this->asJson(['success' => false]);
                }
            }


        }

        return $this->asJson(['success' => true]);
    }
}
