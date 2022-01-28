<?php

namespace percipiolondon\attendees\controllers;

use craft\web\Controller;
use percipiolondon\attendees\records\Logs;

class LogController extends Controller
{
    protected $allowAnonymous = [];

    public function actionLogs(int $eventId)
    {
        $logs = Logs::find()
            ->where([
                'eventId' => $eventId
            ])
            ->andWhere(['>=', 'UNIX_TIMESTAMP(dateCreated)', strtotime("-1 month")])
            ->orderBy('dateCreated DESC')
            ->all();

        return $this->asJson([
            "logs" => $logs
        ]);
    }
}
