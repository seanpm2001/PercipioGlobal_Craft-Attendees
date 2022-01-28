<?php

namespace percipiolondon\attendees\controllers;

use craft\elements\Entry;
use craft\feeds\GuzzleClient;
use craft\web\Controller;

use Craft;
use League\Csv\Exception;
use League\Csv\Reader;
use percipiolondon\attendees\Attendees;
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

    public function actionAttendees(int $eventId, string $order = 'date', int $limit = 0, int $offset = 0)
    {
        $limit = $limit === 0 ? "*" : $limit;
        $orberByOptions = array(
            'date' => 'dateCreated DESC',
            'org' => 'orgName ASC',
            'attendee' => 'name ASC',
            'approved' => 'approved DESC'
        );
        $orderBy = $orberByOptions[$order] ?? 'dateCreated DESC';

        $attendees = AttendeeRecord::find()
            ->where(['eventId' => $eventId])
            ->orderBy($orderBy)
            ->offset($offset)
            ->limit($limit)
            ->all();

        $count = AttendeeRecord::find()
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

    public function actionSave()
    {
        $this->requireLogin();
        $this->requireAcceptsJson();
        $request = Craft::$app->getRequest();
        $attendee = AttendeeHelper::populateAttendeeFromPost($request);

        $success = $attendee->save(true);
        $savedAttendee = AttendeeRecord::find()
            ->where(['eventId' => $attendee->eventId, 'name' => $attendee->name, 'orgName' => $attendee->orgName, 'email' => $attendee->email])
            ->orderBy('dateCreated DESC')
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

//        $attendeeId = Craft::$app->getRequest()->getRequiredBodyParam('attendeeId');
//        $attendee = AttendeeRecord::findOne(['id' =>$attendeeId ]);

        $attendees = Craft::$app->getRequest()->getRequiredBodyParam('attendees');

        foreach($attendees as $id){
            $attendee = AttendeeRecord::findOne(['id' =>$id ]);

            if(!$attendee){
                throw new HttpException(404, Craft::t('craft-attendees', 'Can not find attendee.'));
            }

            if (!$attendee->delete()) {
                return $this->asJson(['success' => false]);
            }
        }

        return $this->asJson(['success' => true, 'attendees' => $attendees]);
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
