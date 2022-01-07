<?php

namespace percipiolondon\attendees\controllers;

use craft\elements\Entry;
use craft\web\Controller;

use Craft;
use percipiolondon\attendees\elements\Attendee;
use percipiolondon\attendees\records\Attendee as AttendeeRecord;
use percipiolondon\attendees\helpers\Attendee as AttendeeHelper;
use yii\web\HttpException;

class TrainingController extends Controller
{
    protected $allowAnonymous = ['save', 'delete'];

    public function actionIndex()
    {
        return $this->renderTemplate('craft-attendees/dashboard/index', []);
    }

    public function actionDetail(int $eventId, string $site = '*')
    {
        $event = \craft\elements\Entry::find()
            ->id($eventId)
            ->site($site)
            ->anyStatus()
            ->one();

        return $this->renderTemplate('craft-attendees/trainings/detail', [
            'event' => $event
        ]);
    }

    public function actionAttendees(int $eventId, int $hitsPerPage = 0, int $currentPage = 0)
    {
        $offset = $hitsPerPage > 0 && $currentPage > 0 ? $hitsPerPage * $currentPage : 0;
        $limit = $hitsPerPage > 0 ? $hitsPerPage : "*";

        $attendees = Attendee::find()
            ->where(['eventId' => $eventId])
            ->orderBy('dateCreated DESC')
            ->offset($offset)
            ->limit($limit)
            ->all();

        return $this->asJson([
            "meta" => [
                "event" => $eventId,
                "offset" => $offset,
                "limit" => $limit
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
}
