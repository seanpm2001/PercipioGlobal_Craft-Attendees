<?php

namespace percipiolondon\attendees\controllers;

use craft\elements\Entry;
use craft\web\Controller;

use Craft;

class TrainingController extends Controller
{
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
        $this->requirePostRequest();
        $request = Craft::$app->getRequest();

        $this->setSuccessFlash(Craft::t('craft-attendees', 'Attendees saved.'));
        return $this->renderTemplate('craft-attendees/trainings');
    }
}
