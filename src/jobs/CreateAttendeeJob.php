<?php

namespace percipiolondon\attendees\jobs;

use Craft;

use craft\queue\BaseJob;
use percipiolondon\attendees\helpers\Log;
use percipiolondon\attendees\records\Attendee;
use percipiolondon\attendees\helpers\Attendee as AttendeeHelper;

class CreateAttendeeJob extends BaseJob
{
    public $config;

    public function execute($queue)
    {
//        [
//            'id' => 0
//            'event' => '302141'
//            'orgName' => 'Aspirer'
//            'orgUrn' => '111584'
//            'postCode' => null
//            'name' => '142343'
//            'email' => 'Chorlton Park Primary'
//            'jobRole' => 'Manchester'
//            'days' => '352'
//            'newsletter' => '2004'
//            'eventId' => 'Chorlton Park Primary'
//        ]

        $orgName = $this->config['orgName'] ?? '';
        $name = $this->config['name'] ?? '';
        $jobRole = $this->config['jobRole'] ?? '';
        $days = $this->config['days'] ?? '';
        $eventId = $this->config['event'] ?? '';
        $file = $this->config['file'] ?? '';
        $filepath = $this->config['filepath'] ?? '';
        $line = (int)($this->config['line'] ?? 0);

        $identifier = hash('md4', $orgName.$name.$jobRole.$days);

        $attendee = Attendee::find()->where(['identifier' => $identifier])->one();

        if($attendee){
            //log an error that this one is being skipped because he already exists
            Craft::info('Importing row: ' . print_r($this->config, true) . ' attendee exists');

            $errors = (object) array(
                'duplicate' => ['Attendee '.$name.' from '.$orgName.' already exists']
            );

            Log::error(json_encode($errors), $eventId, $file, $filepath, $line, $name);
        }else{
            // save the attendee
            $attendee = AttendeeHelper::populateAttendeeFromArray($this->config, $identifier);

            $attendee->validate();

            if($attendee->errors){
                Craft::info('Importing row: ' . print_r($attendee->errors, true));

                Log::error(json_encode($attendee->errors), $eventId, $file, $filepath, $line, $name);
            }else{
                $attendee->save();
            }
        }

    }

    /**
     * @inheritdoc
     */
    protected function defaultDescription(): string
    {
        return \Craft::t('craft-attendees', 'Save attendee - {name} ({org})', ['name' => $this->config['name'], 'org' => $this->config['orgName']]);
    }
}

