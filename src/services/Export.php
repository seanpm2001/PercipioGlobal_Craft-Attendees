<?php
namespace percipiolondon\attendees\services;

use yii\base\Component;
use yii\data\ArrayDataProvider;
use yii2tech\csvgrid\CsvGrid;
use percipiolondon\attendees\records\Attendee as AttendeeRecord;
use Craft;

class Export extends Component
{
    public function export($type, $school, $start, $end, $site): CsvGrid
    {
        $rsn = '';

        if($site !== '*'){
            $rsn = Craft::$app->getSites()->getSiteById($site)->name;
        }

        switch($type){
            case 'event':
                $exporter = $this->_buildEventArray($rsn, $start, $end, $site);
                break;
            case 'subscriptions':
                $exporter = $this->_buildSubscriptionsArray();
                break;
            case 'school-attendee':
                $exporter = $this->_buildSchoolAttendeeArray();
                break;
            case 'school-unique':
                $exporter = $this->_buildSchoolUniqueArray();
                break;
            default:
                $exporter = $this->_buildAttendeesArray($rsn, $start, $end, $site);
        }

        return $exporter;
    }

    private function _buildAttendeesArray($rsn, $start, $end, $site){

//        if($site != '*'){
//            $query = AttendeeRecord::find()
//                ->where(['siteId' => $site]);
//        }else{
//            $query = AttendeeRecord::find();
//        }
//
//        $results = [];
//
//        foreach($query->all() as $entry){
//
//            $school = $rsn;
//
//            if(!$school){
//                $school = Craft::$app->getSites()->getSiteById($entry->siteId)->name;
//            }
//
//            $event = \craft\elements\Entry::find()
//                ->section('events')
//                ->id($entry->eventId)
//                ->nextUpcomingEvent(['and', ">= {$start}", "< {$end}"])
//                ->one();
//
////            if($event){
//                $result = [
//                    'rsn' => $school,
//                    'eventId' => $entry->eventId ?? 'null',
//                    'eventData' => $event->nextUpcomingEvent ?? 'null',
//                    'name' => $entry->name,
//                    'email' => $entry->email,
//                    'role' => $entry->jobRole,
//                    'modulesAttended' => $entry->days,
//                    'mailingList' => $entry->newsletter
//                ];
//
//                $results[] = $result;
////            }
//        }

        $siteInnerJoin = $site == '*' ? 'INNER JOIN sites s ON c.siteId = s.id' : '';
        $siteWhere = $site != '*' ? 'AND c.siteId = '.$site : '';


        $connection = \Yii::$app->getDb();
        $sql = '
            SELECT handle AS rsn, elementId as eventID, title AS training, orgName, priority, orgUrn, postCode, a.name as name, email, jobRole, days AS modulesAttended, anonymous
                FROM entries e
                INNER JOIN content c ON e.id = c.elementId
                INNER JOIN sites s ON c.siteId = s.id
                INNER JOIN attendees_attendees a ON e.id = a.eventId
                '.$siteInnerJoin.'

                WHERE e.`id` IN (

                    SELECT eventId FROM
                    (
                        SELECT eventId, RSN, Training, MAX(lastTraingingDate) AS lastTrainingDate FROM
                        (
                            SELECT e.id AS eventId, s.handle AS RSN, c.title AS training,
                            CASE
                            WHEN d1.field_eventDate_startDateTime IS NOT NULL THEN DATE_FORMAT(d1.field_eventDate_startDateTime, "%d-%m-%Y")
                            WHEN d2.field_eventDate_startDateTime IS NOT NULL THEN DATE_FORMAT(d2.field_eventDate_startDateTime, "%d-%m-%Y")
                            ELSE DATE_FORMAT(d3.field_eventDate_startDateTime, "%d-%m-%Y") END AS lastTraingingDate
                            FROM entries e
                                INNER JOIN matrixblocks m ON m.ownerId = e.id
                                INNER JOIN content c ON e.id = c.elementId
                                INNER JOIN elements em ON e.id = em.id
                                INNER JOIN sites s on s.id = c.siteId
                                LEFT JOIN matrixcontent_eventdatestime d1 ON d1.elementId = m.id
                                LEFT JOIN matrixcontent_eventdatestimeonline d2 ON d2.elementId = m.id
                                LEFT JOIN matrixcontent_eventdatestime d3 ON d3.elementId = m.id
                            WHERE e.sectionId = 15
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
                                    ) AS a
                                    GROUP BY a.elementId, field_eventDate_startDateTime
                                ) AS af
                                WHERE eventDate BETWEEN "' . $start . '" AND  "' . $end . '"
                                UNION
                                    SELECT  elementId FROM
                                    (
                                        SELECT elementId, field_eventDate_startDateTime AS eventDate FROM
                                        (
                                            SELECT elementId, field_eventDate_startDateTime
                                                FROM matrixcontent_eventdatestimeonline m
                                                INNER JOIN elements e ON e.id = m.elementId
                                                WHERE e.enabled = 1
                                                    ORDER BY field_eventDate_startDateTime DESC
                                        )AS b
                                        GROUP BY b.elementId, field_eventDate_startDateTime
                                    ) AS bf
                                    WHERE eventDate BETWEEN "' . $start . '" AND  "' . $end . '"
                                UNION
                                    SELECT elementId FROM
                                    (
                                        SELECT elementId, field_eventDate_startDateTime AS eventDate FROM
                                        (
                                            SELECT elementId, field_eventDate_startDateTime FROM matrixcontent_eventhybriddatestime m
                                                INNER JOIN elements e ON e.id = m.elementId
                                                    WHERE e.enabled = 1
                                                        ORDER BY field_eventDate_startDateTime DESC
                                        ) AS c
                                        GROUP BY c.elementId, field_eventDate_startDateTime
                                    ) AS cf
                                    WHERE eventDate BETWEEN "' . $start . '" AND  "' . $end . '"
                            )
                            ORDER BY RSN ASC, lastTraingingDate DESC
                        ) as results
                        GROUP BY eventId, RSN, Training
                        ORDER BY RSN ASC, lastTrainingDate DESC
                    ) as export
                )
                '.$siteWhere.'
                ORDER BY RSN ASC, eventID ASC
        ';

        $command = $connection->createCommand($sql);
        $results = $command->queryAll();

        return new CsvGrid([
            'dataProvider' => new ArrayDataProvider([
                'allModels' => $results
            ]),
            'columns' => [
                [
                    'attribute' => 'rsn',
                ],
                [
                    'attribute' => 'eventID',
                ],
                [
                    'attribute' => 'training',
                ],
                [
                    'attribute' => 'orgName',
                ],
                [
                    'attribute' => 'priority',
                ],
                [
                    'attribute' => 'orgUrn',
                ],
                [
                    'attribute' => 'postCode',
                ],
                [
                    'attribute' => 'name',
                ],
                [
                    'attribute' => 'email',
                ],
                [
                    'attribute' => 'jobRole',
                ],
                [
                    'attribute' => 'modulesAttended',
                ],
                [
                    'attribute' => 'mailingList',
                ],
                [
                    'attribute' => 'anonymous',
                ],
            ],
            'maxEntriesPerFile' => 2000, // limit max rows per single file
            'resultConfig' => [
                'forceArchive' => true
            ],
        ]);
    }

    private function _buildEventArray($rsn, $start, $end, $site){

//        $query = AttendeeRecord::find()
//            ->where(['siteId' => $site]);
//
//        $results = [];
//
//        foreach($query->all() as $entry){
//
//            $event = \craft\elements\Entry::find()
//                ->section('events')
//                ->id($entry->eventId)
//                ->nextUpcomingEvent(['and', ">= {$start}", "< {$end}"])
//                ->one();
//
//            if($event){
//                $result = [
//                    'rsn' => $rsn,
//                    'eventId' => $entry->eventId,
//                    'event' => $event->title,
//                    'eventData' => $event->nextUpcomingEvent,
//                ];
//
//                $results[] = $result;
//            }
//        }

        $connection = \Yii::$app->getDb();
        $sql = '
            SELECT eventId, RSN, Training, MAX(lastTraingingDate) AS lastTrainingDate FROM (
            SELECT
               e.id AS eventId,
                  s.handle AS RSN,
                  c.title AS training,
                  CASE
                  WHEN d1.field_eventDate_startDateTime IS NOT NULL THEN DATE_FORMAT(d1.field_eventDate_startDateTime, "%d-%m-%Y")
                  WHEN d2.field_eventDate_startDateTime IS NOT NULL THEN DATE_FORMAT(d2.field_eventDate_startDateTime, "%d-%m-%Y")
                     ELSE DATE_FORMAT(d3.field_eventDate_startDateTime, "%d-%m-%Y") END
                  AS lastTraingingDate
            FROM entries e
               INNER JOIN matrixblocks m ON m.ownerId = e.id
               INNER JOIN content c ON e.id = c.elementId
               INNER JOIN elements em ON e.id = em.id
               INNER JOIN sites s on s.id = c.siteId
               LEFT JOIN matrixcontent_eventdatestime d1 ON d1.elementId = m.id
               LEFT JOIN matrixcontent_eventdatestimeonline d2 ON d2.elementId = m.id
               LEFT JOIN matrixcontent_eventdatestime d3 ON d3.elementId = m.id
                  WHERE e.sectionId = 15
                  AND em.revisionId IS NULL
                  AND em.draftId IS NULL
                  AND e.authorId IS NOT NULL
                  AND e.postDate IS NOT NULL
                  AND e.id IN
                    (
                    SELECT distinct(sourceId)
                        FROM content c
                        INNER JOIN elements em ON em.id = c.elementId
                        INNER JOIN relations r ON c.elementId = r.targetId
                        WHERE c.title = "Accelerator Fund"
                    )
                  AND m.id IN
                    (
                        SELECT distinct(elementId) FROM (
                           SELECT elementId, field_eventDate_startDateTime AS eventDate FROM(
                           SELECT elementId, field_eventDate_startDateTime FROM matrixcontent_eventdatestime m
                              INNER JOIN elements e ON e.id = m.elementId
                              WHERE e.enabled = 1
                                 ORDER BY field_eventDate_startDateTime DESC
                                 )AS a
                              GROUP BY a.elementId, field_eventDate_startDateTime
                           )  as af
                           WHERE eventDate BETWEEN "2021-09-14" AND "2022-02-14"
                    UNION
                       SELECT  elementId FROM (
                       SELECT elementId, field_eventDate_startDateTime AS eventDate FROM(
                       SELECT elementId, field_eventDate_startDateTime FROM matrixcontent_eventdatestimeonline m
                       INNER JOIN elements e ON e.id = m.elementId
                          WHERE e.enabled = 1
                             ORDER BY field_eventDate_startDateTime DESC
                             )AS b
                          GROUP BY b.elementId, field_eventDate_startDateTime
                       ) as bf
                      WHERE eventDate BETWEEN "2021-09-14" AND "2022-02-14"
                    UNION
                       SELECT elementId FROM (
                       SELECT elementId, field_eventDate_startDateTime AS eventDate FROM(
                       SELECT elementId, field_eventDate_startDateTime FROM matrixcontent_eventhybriddatestime m
                          INNER JOIN elements e ON e.id = m.elementId
                          WHERE e.enabled = 1
                             ORDER BY field_eventDate_startDateTime DESC
                             ) AS c
                          GROUP BY c.elementId, field_eventDate_startDateTime
                       ) as cf
                       WHERE eventDate BETWEEN "2021-09-14" AND "2022-02-14"
                       )
                      ORDER BY RSN ASC, lastTraingingDate DESC
                      ) as results
                      GROUP BY eventId, RSN, Training
                      ORDER BY RSN ASC, lastTrainingDate DESC
        ';
        $command = $connection->createCommand($sql);
        $results = $command->queryAll();

        Craft::dd($results);

        return new CsvGrid([
            'dataProvider' => new ArrayDataProvider([
                'allModels' => $results
            ]),
            'columns' => [
                [
                    'attribute' => 'rsn',
                ],
                [
                    'attribute' => 'eventId',
                ],
                [
                    'attribute' => 'event',
                ],
                [
                    'attribute' => 'eventData',
                ]
            ],
            'maxEntriesPerFile' => 2000, // limit max rows per single file
            'resultConfig' => [
                'forceArchive' => true
            ],
        ]);

    }

    private function _buildSubscriptionsArray(){

        $query = AttendeeRecord::find();
        return new CsvGrid([
            'query' => $query,
            'maxEntriesPerFile' => 2000, // limit max rows per single file
            'resultConfig' => [
                'forceArchive' => true
            ],
        ]);

    }

    private function _buildSchoolAttendeeArray(){

        $query = AttendeeRecord::find();
        return new CsvGrid([
            'query' => $query,
            'maxEntriesPerFile' => 2000, // limit max rows per single file
            'resultConfig' => [
                'forceArchive' => true
            ],
        ]);

    }

    private function _buildSchoolUniqueArray(){

        $query = AttendeeRecord::find();
        return new CsvGrid([
            'query' => $query,
            'maxEntriesPerFile' => 2000, // limit max rows per single file
            'resultConfig' => [
                'forceArchive' => true
            ],
        ]);

    }
}
