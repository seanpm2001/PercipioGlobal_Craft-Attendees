<?php

namespace percipiolondon\attendees\jobs;

use Craft;

use craft\queue\BaseJob;
use craft\helpers\Json;
use percipioglobal\notifications\Notifications;
use percipiolondon\attendees\helpers\Log;
use percipiolondon\attendees\records\Attendee;
use percipiolondon\attendees\helpers\Attendee as AttendeeHelper;
use percipiolondon\notifications\CsvAlert;
use yii\log\Logger;

class CreateAttendeeJob extends BaseJob
{
    public $config;

    public function execute($queue)
    {
        try {
            $orgName = utf8_encode($this->config['orgName'] ?? '');
            $name = utf8_encode($this->config['name'] ?? '');
            $jobRole = utf8_encode($this->config['jobRole'] ?? '');
            $days = utf8_encode($this->config['days'] ?? '');
            $eventId = $this->config['event'] ?? '';
            $file = $this->config['file'] ?? '';
            $filepath = $this->config['filepath'] ?? '';
            $line = (int)($this->config['line'] ?? 0);
            $totalLines = (int)($this->config['total'] ?? 0);

            $identifier = hash('md4', $orgName.$name.$jobRole.$days);

            $attendee = Attendee::find()->where(['eventId' => $eventId, 'identifier' => $identifier])->one();

            if($attendee){
                //log an error that this one is being skipped because he already exists
//                Craft::info('Importing row: ' . print_r($this->config, true) . ' attendee exists');

                $errors = (object) array(
                    'duplicate' => ['Attendee '.$name.' from '.$orgName.' already exists']
                );

                $log = [
                    'status' => 'error',
                    'message' => Json::encode($errors),
                    'name' => $name,
                    'eventId' => $eventId,
                    'file' => $file,
                    'filepath' => $filepath,
                    'line' => $line,
                    'totalLines' => $totalLines
                ];

                Log::log($log);
            } else {
                // save the attendee
                $attendee = AttendeeHelper::populateAttendeeFromArray($this->config, $identifier);

                $attendee->validate();

                if($attendee->errors){

                    $log = [
                        'status' => 'error',
                        'message' => Json::encode($attendee->errors),
                        'name' => $name,
                        'eventId' => $eventId,
                        'file' => $file,
                        'filepath' => $filepath,
                        'line' => $line,
                        'totalLines' => $totalLines
                    ];

                    Log::log($log);

                    Craft::error('Attendees error when saving, log: '.json_encode($log), 'attendees');
                }else{
                    $success = (object) array(
                        'success' => ['Attendee '.$name.' from '.$orgName.' succeeded']
                    );

                    $log = [
                        'status' => 'success',
                        'message' => Json::encode($success),
                        'name' => $name,
                        'eventId' => $eventId,
                        'file' => $file,
                        'filepath' => $filepath,
                        'line' => $line,
                        'totalLines' => $totalLines
                    ];

                    Log::log($log);

                    $attendee->save();
                }
            }

        } catch (\Exception $e) {
            $errors = (object) array(
                'php' => ['Something went wrong when saving the attendee. Please check your CSV file for foreign characters. If the CSV has no issues, please contact RSN.']
            );

            $log = [
                'status' => 'error',
                'message' => Json::encode($errors),
                'name' => $name,
                'eventId' => $eventId,
                'file' => $file,
                'filepath' => $filepath,
                'line' => $line,
                'totalLines' => $totalLines
            ];

            Log::log($log);

            Craft::error($e->getMessage(). ', log: '.json_encode($log), 'attendees');

            try {
                $csvAlert = new CsvAlert();
                $csvAlert->message = $e->getMessage();
                $csvAlert->log = $log;
                Notifications::getInstance()->notificationsService->send($csvAlert);
            } catch (\Exception $e) {
                //ignore is slack notifications doesn't work because of bulk spam
            }

        }

    }

    /**
     * @inheritdoc
     */
    protected function defaultDescription(): string
    {
        return Craft::t('craft-attendees', 'Save attendee - {name} ({org})', ['name' => utf8_encode($this->config['name'] ?? 'Unkown'), 'org' => utf8_encode($this->config['orgName'] ?? 'Unknown')]);
    }
}

