<?php

namespace percipiolondon\attendees\controllers;

use craft\web\Controller;
use percipiolondon\attendees\Attendees;
use percipiolondon\attendees\records\Attendee as AttendeeRecord;
use percipiolondon\attendees\records\FollowOnSupport;
use percipiolondon\attendees\records\FollowOnSupportOptions;

class DashboardController extends Controller
{
    public function actionFetchEvents(string $site = '', string $period = '3')
    {
        $site = $site === '2' ? '' : $site;

        if(strlen($period) > 1){
            $end = strtotime('31 july ' . (int)$period );
            $start = strtotime('01 september ' . ((int)$period - 1) );
        }else{
            $end = strtotime('today');
            $start = strtotime('-' . $period . ' Months');
        }

        $whereSiteId = strlen($site) > 0 ? 'AND m.siteId = ' . $site : '';
        $connection = \Yii::$app->getDb();

        $sql = "SELECT distinct(e.id) FROM entries e
           INNER JOIN matrixblocks m ON m.ownerId = e.id
           INNER JOIN content c ON e.id = c.elementId
           INNER JOIN elements em ON e.id = em.id
              WHERE e.sectionId = 15
              AND em.revisionId IS NULL
              AND em.draftId IS NULL
              AND e.authorId IS NOT NULL
              AND e.postDate IS NOT NULL
              AND m.id IN
                 (
                    SELECT elementId FROM (
                       SELECT elementId, field_eventDate_startDateTime AS eventDate FROM(
                       SELECT elementId, field_eventDate_startDateTime FROM matrixcontent_eventdatestime m
                          INNER JOIN elements e ON e.id = m.elementId
                          WHERE e.enabled = 1
                             " . $whereSiteId . "
                             ORDER BY field_eventDate_startDateTime DESC
                             )AS a
                          GROUP BY a.elementId, field_eventDate_startDateTime
                       )  as af
                       WHERE UNIX_TIMESTAMP(eventDate) BETWEEN " . $start . " AND  " . $end . "
                    UNION
                       SELECT elementId FROM (
                       SELECT elementId, field_eventDate_startDateTime AS eventDate FROM(
                       SELECT elementId, field_eventDate_startDateTime FROM matrixcontent_eventdatestimeonline m
                       INNER JOIN elements e ON e.id = m.elementId
                          WHERE e.enabled = 1
                             " . $whereSiteId . "
                             ORDER BY field_eventDate_startDateTime DESC
                             )AS b
                          GROUP BY b.elementId, field_eventDate_startDateTime
                       ) as bf
                       WHERE UNIX_TIMESTAMP(eventDate) BETWEEN " . $start . " AND  " . $end . "
                    UNION
                       SELECT  elementId FROM (
                       SELECT elementId, field_eventDate_startDateTime AS eventDate FROM(
                       SELECT elementId, field_eventDate_startDateTime FROM matrixcontent_eventhybriddatestime m
                          INNER JOIN elements e ON e.id = m.elementId
                          WHERE e.enabled = 1
                             " . $whereSiteId . "
                             ORDER BY field_eventDate_startDateTime DESC
                             ) AS c
                          GROUP BY c.elementId, field_eventDate_startDateTime
                       ) as cf
                       WHERE UNIX_TIMESTAMP(eventDate) BETWEEN " . $start . " AND  " . $end . "
                 )";
        $command = $connection->createCommand($sql);
        $events = $command->queryAll();

        $attendees = [];
        $followonsupport = [];

        foreach($events as $event){

            $evtAttendees = AttendeeRecord::find()
                ->where(['eventId' => $event['id']])
                ->all();

            $evtFollow = FollowOnSupport::find()
                ->where(['eventId' => $event['id']])
                ->all();

            array_push($attendees, ...$evtAttendees);
            array_push($followonsupport, ...$evtFollow);
        }

        return $this->asJson([
            "events" => $events,
            "attendees" => $attendees,
            "follow_on_support" => $followonsupport,
            "follow_on_support_options" => FollowOnSupportOptions::find()->all()
        ]);

    }
}
