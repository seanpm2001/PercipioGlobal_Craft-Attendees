<?php

namespace percipiolondon\attendees\controllers;

use Craft;
use craft\web\Controller;
use percipiolondon\attendees\Attendees;
use percipiolondon\attendees\records\Attendee as AttendeeRecord;
use percipiolondon\attendees\records\FollowOnSupport;
use percipiolondon\attendees\records\FollowOnSupportOptions;

class DashboardController extends Controller
{
    public function actionEvents()
    {
        $this->requireLogin();
        $request = Craft::$app->getRequest();

        $site = $request->getBodyParam('site');
        $period = $request->getBodyParam('period');

        $site = $site === '2' ? '' : $site;

        if(strlen($period) > 1){
            $end = strtotime('31 july ' . (int)$period );
            $start = strtotime('01 september ' . ((int)$period - 1) );
        }else{
            $end = strtotime('today');
            $start = strtotime('-' . $period . ' Months');
        }

        $whereSiteId = strlen($site) > 0 ? 'AND c.siteId = ' . $site : '';
        $connection = \Yii::$app->getDb();

        $sql = "
            SELECT
            DISTINCT(eventId) AS id,
            COUNT(DISTINCT(attendeeId)) AS totalAttendees,
            COUNT(DISTINCT(orgUrn)) AS totalSchools,
            SUM(priority) AS totalPriority
            FROM
            (
                SELECT e.id AS eventId, a.id AS attendeeId, a.priority, a.orgUrn
                    FROM entries e
                    INNER JOIN matrixblocks m ON m.ownerId = e.id
                    INNER JOIN content c ON e.id = c.elementId
                    INNER JOIN elements em ON e.id = em.id
                    INNER JOIN sites s on s.id = c.siteId
                    LEFT JOIN attendees_attendees a on a.eventId = e.id
                    LEFT JOIN matrixcontent_eventdatestime d1 ON d1.elementId = m.id
                    LEFT JOIN matrixcontent_eventdatestimeonline d2 ON d2.elementId = m.id
                    LEFT JOIN matrixcontent_eventdatestime d3 ON d3.elementId = m.id
                        WHERE e.sectionId = 15
                        AND a.approved = 1
                        " . $whereSiteId . "
                        AND em.revisionId IS NULL
                        AND em.draftId IS NULL
                        AND e.authorId IS NOT NULL
                        AND e.postDate IS NOT NULL
                        AND m.id IN
                        (
                            SELECT distinct(elementId) FROM
                            (
                                SELECT elementId, field_eventDate_startDateTime AS eventDate FROM
                                (
                                    SELECT elementId, field_eventDate_startDateTime FROM matrixcontent_eventdatestime m
                                        INNER JOIN elements e ON e.id = m.elementId
                                        WHERE e.enabled = 1
                                            ORDER BY field_eventDate_startDateTime DESC
                                )
                                AS a
                                GROUP BY a.elementId, field_eventDate_startDateTime
                            )
                            AS af
                            WHERE UNIX_TIMESTAMP(eventDate) BETWEEN " . $start . " AND  " . $end . "
                            UNION
                                SELECT  elementId FROM
                                (
                                    SELECT elementId, field_eventDate_startDateTime AS eventDate FROM
                                    (
                                        SELECT elementId, field_eventDate_startDateTime FROM matrixcontent_eventdatestimeonline m
                                        INNER JOIN elements e ON e.id = m.elementId
                                            WHERE e.enabled = 1
                                                ORDER BY field_eventDate_startDateTime DESC
                                    )
                                    AS b
                                    GROUP BY b.elementId, field_eventDate_startDateTime
                                )
                                AS bf
                                WHERE UNIX_TIMESTAMP(eventDate) BETWEEN " . $start . " AND  " . $end . "
                            UNION
                                SELECT elementId FROM
                                (
                                    SELECT elementId, field_eventDate_startDateTime AS eventDate FROM
                                    (
                                        SELECT elementId, field_eventDate_startDateTime FROM matrixcontent_eventhybriddatestime m
                                            INNER JOIN elements e ON e.id = m.elementId
                                            WHERE e.enabled = 1
                                                ORDER BY field_eventDate_startDateTime DESC
                                    )
                                    AS c
                                    GROUP BY c.elementId, field_eventDate_startDateTime
                                ) AS cf
                                WHERE UNIX_TIMESTAMP(eventDate) BETWEEN " . $start . " AND  " . $end . "
                        )


            )
            AS res
            GROUP BY id
        ";
        $command = $connection->createCommand($sql);
        $events = $command->queryAll();

        $attendees = [];
        $followonsupport = [];
        $unverifiedAttendees = [];
        $totals = [
            'events' => 0,
            'attendees' => 0,
            'schools' => 0,
            'priority' => 0
        ];

        foreach($events as $event){

            $evtAttendees = AttendeeRecord::find()
                ->where(['eventId' => $event['id']])
                ->andWhere(['approved' => 1])
                ->all();

            $evtUnverifiedAttendees = AttendeeRecord::find()
                ->where(['eventId' => $event['id']])
                ->andWhere(['approved' => 0])
                ->all();

            $evtFollow = FollowOnSupport::find()
                ->where(['eventId' => $event['id']])
                ->all();

            $totals['events'] = $totals['events'] + 1;
            $totals['attendees'] = $totals['attendees'] + $event['totalAttendees'];
            $totals['schools'] = $totals['schools'] + $event['totalSchools'];
            $totals['priority'] = $totals['priority'] + $event['totalPriority'];

            $attendees[] = $evtAttendees;
            $followonsupport[] = $evtFollow;
            $unverifiedAttendees[] = $evtUnverifiedAttendees;
        }

        return $this->asJson([
            "events" => $events,
            "attendees" => $attendees,
            'unverified_attendees' => $unverifiedAttendees,
            "follow_on_support" => $followonsupport,
            "follow_on_support_options" => FollowOnSupportOptions::find()->all(),
            "totals" => $totals
        ]);

//        $attendees = [];
//        $followonsupport = [];
//
//        foreach($events as $event){
//
//            $evtAttendees = AttendeeRecord::find()
//                ->where(['eventId' => $event['id']])
//                ->all();
//
//            $evtFollow = FollowOnSupport::find()
//                ->where(['eventId' => $event['id']])
//                ->all();
//
//            array_push($attendees, ...$evtAttendees);
//            array_push($followonsupport, ...$evtFollow);
//        }
//
//        return $this->asJson([
//            "events" => $events,
//            "attendees" => $attendees,
//            "follow_on_support" => $followonsupport,
//            "follow_on_support_options" => FollowOnSupportOptions::find()->all(),
//        ]);
    }

    public function actionFetch()
    {
        $this->requireLogin();
        $request = Craft::$app->getRequest();

        $ids = $request->getBodyParam('events');

        $events = json_decode($ids);
        $attendees = [];
        $followonsupport = [];

        foreach($events as $event){

            $evtAttendees = AttendeeRecord::find()
                ->where(['eventId' => $event])
                ->all();

            $evtFollow = FollowOnSupport::find()
                ->where(['eventId' => $event])
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
