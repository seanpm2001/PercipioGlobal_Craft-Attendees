<?php

namespace percipiolondon\attendees\controllers;

use craft\elements\Entry;
use craft\web\Controller;

use Craft;
use percipiolondon\attendees\records\Attendee as AttendeeRecord;
use percipiolondon\attendees\helpers\Attendee as AttendeeHelper;

class TrainingController extends Controller
{
    protected $allowAnonymous = ['save'];

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

        $attendees = AttendeeRecord::find()
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

        $response = (object)[
            "success" => $success,
            "errors" => $attendee->getErrors(),
        ];

        return $this->asJson($response);
    }
}
