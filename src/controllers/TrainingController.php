<?php

namespace percipiolondon\attendees\controllers;

use craft\elements\Entry;
use craft\web\Controller;

use Craft;
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

    public function actionSave()
    {
        $this->requireLogin();
        $this->requireAcceptsJson();
        $request = Craft::$app->getRequest();
        $attendee = AttendeeHelper::populateAttendeeFromPost($request);

        $success = Craft::$app->getElements()->saveElement($attendee);

//        Craft::dd($attendee->getErrorSummary());

        $response = (object)[
            "success" => $success,
            "errors" => $attendee->getErrors(),
        ];

        return $this->asJson($response);

//        $this->setSuccessFlash(Craft::t('craft-attendees', 'Attendees saved.'));
//        return $this->renderTemplate('craft-attendees/trainings');
    }
}
