<?php
namespace percipiolondon\attendees\services;

use percipiolondon\attendees\Attendees;
use percipiolondon\attendees\records\Attendee;
use yii\base\Component;
use yii\data\ArrayDataProvider;
use yii2tech\csvgrid\CsvGrid;
use percipiolondon\attendees\models\Export as ExportModel;
use Craft;

class Export extends Component
{
    public $MAX_ENTRIES_PER_FILE = 2000;

    public function export(ExportModel $exportModel): CsvGrid
    {
        $exporter = match ($exportModel->exportType) {
            'event' => $this->_buildEventExport($exportModel->eventType, $exportModel->start, $exportModel->end, $exportModel->site, $exportModel->school, $exportModel->tag),
            'subscriptions' => $this->_buildSubscriptionsExport($exportModel->eventType, $exportModel->start, $exportModel->end, $exportModel->site, $exportModel->school, $exportModel->tag),
            'school-attendee' => $this->_buildSchoolAttendeeExport($exportModel->eventType, $exportModel->start, $exportModel->end, $exportModel->site, $exportModel->school, $exportModel->tag),
            'school-unique' => $this->_buildSchoolUniqueExport($exportModel->eventType, $exportModel->start, $exportModel->end, $exportModel->site, $exportModel->school, $exportModel->tag),
            default => $this->_buildAttendeesExport($exportModel->eventType, $exportModel->start, $exportModel->end, $exportModel->site, $exportModel->school, $exportModel->tag),
        };

        return $exporter;
    }

    private function _buildAttendeesExport(string $eventType, string $start, string $end, string $site, string $priority, string $tag): CsvGrid
    {
        $connection = \Yii::$app->getDb();
        $sql = $this->attendeesQuery($eventType, $site, $start, $end, $priority, $tag);
        $command = $connection->createCommand($sql);
        $results = $command->queryAll();

        return new CsvGrid([
            'dataProvider' => new ArrayDataProvider([
                'allModels' => $results
            ]),
            'columns' => [
                ['attribute' => 'RSN'],
                ['attribute' => 'eventID'],
                ['attribute' => 'training'],
                ['attribute' => 'eventType'],
                ['attribute' => 'orgName'],
                ['attribute' => 'priority'],
                ['attribute' => 'orgUrn'],
                ['attribute' => 'postCode'],
                ['attribute' => 'name'],
                ['attribute' => 'email'],
                ['attribute' => 'jobRole'],
                ['attribute' => 'modulesAttended'],
                ['attribute' => 'mailingList'],
                ['attribute' => 'anonymous'],
            ],
            'maxEntriesPerFile' => $this->MAX_ENTRIES_PER_FILE,
            'resultConfig' => [
                'forceArchive' => true
            ],
        ]);
    }

    private function _buildEventExport(string $eventType, string $start, string $end, string $site, string $priority, string $tag): CsvGrid
    {
        $connection = \Yii::$app->getDb();
        $sql = $this->eventsQuery($eventType, $site, $start, $end, $priority, $tag);
        $command = $connection->createCommand($sql);
        $results = $command->queryAll();

        return new CsvGrid([
            'dataProvider' => new ArrayDataProvider([
                'allModels' => $results
            ]),
            'columns' => [
                ['attribute' => 'RSN'],
                ['attribute' => 'eventId'],
                ['attribute' => 'training'],
                ['attribute' => 'lastTrainingDate']
            ],
            'maxEntriesPerFile' => $this->MAX_ENTRIES_PER_FILE,
            'resultConfig' => [
                'forceArchive' => true
            ],
        ]);
    }

    private function _buildSubscriptionsExport(string $eventType, string $start, string $end, string $site, string $priority, string $tag): CsvGrid
    {
        $connection = \Yii::$app->getDb();
        $sql = $this->attendeesQuery($eventType, $site, $start, $end, $priority, $tag);
        $command = $connection->createCommand($sql);
        $results = $command->queryAll();

        $subscriptionResults = [];

        foreach($results as $result){
            if(strlen($result['email']) > 0 && $result['mailingList'] == '1') {
                $name = Attendees::getInstance()->nameparser->parse_name($result['name']);

                $subscriptionResults[] = [
                    'RSN' => $result['RSN'],
                    'fname' => $name['fname'],
                    'lname' => $name['lname'],
                    'fullname' => $result['name'],
                    'email' => $result['email']
                ];
            }
        }

        return new CsvGrid([
            'dataProvider' => new ArrayDataProvider([
                'allModels' => $subscriptionResults
            ]),
            'columns' => [
                ['attribute' => 'RSN'],
                ['attribute' => 'fname'],
                ['attribute' => 'lname'],
                ['attribute' => 'fullname'],
                ['attribute' => 'email']
            ],
            'maxEntriesPerFile' => $this->MAX_ENTRIES_PER_FILE,
            'resultConfig' => [
                'forceArchive' => true
            ],
        ]);
    }

    private function _buildSchoolAttendeeExport(string $eventType, string $start, string $end, string $site, string $priority, string $tag): CsvGrid
    {
        $connection = \Yii::$app->getDb();
        $sql = $this->attendeeSchoolsQuery($eventType, $site, $start, $end, $priority, $tag);
        $command = $connection->createCommand($sql);
        $results = $command->queryAll();

        $urns = array_column($results, 'urn');

        $response = Attendees::getInstance()->metaseed->attendeeSchools($urns);

        $exports = [];

        foreach($results as $attendee){

            $school = null;

            foreach($response['data'] as $resSchool){
                if($attendee['urn'] === $resSchool['urn'] && $school === null){
                    $school = $resSchool;
                    break;
                }
            }

            if($school){
                $attendeeExp = [
                    'RSN' => $attendee['rsn'],
                    'eventID' => $attendee['eventId'],
                    'training' => $attendee['training'],
                    'modulesAttended' => $attendee['days'],
                ];

                $exports[] = array_merge($attendeeExp, $school);
            }
        }

        return new CsvGrid([
            'dataProvider' => new ArrayDataProvider([
                'allModels' => $exports
            ]),
            'columns' => [
                ['attribute' => 'RSN'],
                ['attribute' => 'eventID'],
                ['attribute' => 'training'],
                ['attribute' => 'modulesAttended'],
                ['attribute' => 'urn'],
                ['attribute' => 'la'],
                ['attribute' => 'laNum'],
                ['attribute' => 'estab'],
                ['attribute' => 'schName'],
                ['attribute' => 'street'],
                ['attribute' => 'locality'],
                ['attribute' => 'town'],
                ['attribute' => 'region'],
                ['attribute' => 'postcode'],
                ['attribute' => 'status'],
                ['attribute' => 'type'],
                ['attribute' => 'phase'],
                ['attribute' => 'ageFrom'],
                ['attribute' => 'ageTo'],
                ['attribute' => 'pup'],
                ['attribute' => 'ppPup'],
                ['attribute' => 'pupBoys'],
                ['attribute' => 'pupGirls'],
                ['attribute' => 'percFsm'],
                ['attribute' => 'ppPerc'],
                ['attribute' => 'ppAllocation'],
                ['attribute' => 'headTitle'],
                ['attribute' => 'headFirstName'],
                ['attribute' => 'headLastName'],
                ['attribute' => 'headRole'],
                ['attribute' => 'schTel'],
                ['attribute' => 'ntp_eligible'],
                ['attribute' => 'priority'],
                ['attribute' => 'censusDate'],
                ['attribute' => 'lastUpdated'],
            ],
            'maxEntriesPerFile' => $this->MAX_ENTRIES_PER_FILE,
            'resultConfig' => [
                'forceArchive' => true
            ],
        ]);
    }

    private function _buildSchoolUniqueExport(string $eventType, string $start, string $end, string $site, string $priority, string $tag): CsvGrid
    {
        $connection = \Yii::$app->getDb();
        $sql = $this->attendeeSchoolsQuery($eventType, $site, $start, $end, $priority, $tag);
        $command = $connection->createCommand($sql);
        $results = $command->queryAll();

        $urns = [];

        foreach($results as $result){
            $urns[] = $result['orgUrn'];
        }

        $urns = array_unique($urns);

        $response = Attendees::getInstance()->metaseed->attendeeSchools($urns);

        return new CsvGrid([
            'dataProvider' => new ArrayDataProvider([
                'allModels' => isset($response['data'][0]['urn']) ? $response['data'] : []
            ]),
            'columns' => [
                ['attribute' => 'urn'],
                ['attribute' => 'la'],
                ['attribute' => 'laNum'],
                ['attribute' => 'estab'],
                ['attribute' => 'schName'],
                ['attribute' => 'street'],
                ['attribute' => 'locality'],
                ['attribute' => 'town'],
                ['attribute' => 'region'],
                ['attribute' => 'postcode'],
                ['attribute' => 'status'],
                ['attribute' => 'type'],
                ['attribute' => 'phase'],
                ['attribute' => 'ageFrom'],
                ['attribute' => 'ageTo'],
                ['attribute' => 'pup'],
                ['attribute' => 'ppPup'],
                ['attribute' => 'pupBoys'],
                ['attribute' => 'pupGirls'],
                ['attribute' => 'percFsm'],
                ['attribute' => 'ppPerc'],
                ['attribute' => 'ppAllocation'],
                ['attribute' => 'headTitle'],
                ['attribute' => 'headFirstName'],
                ['attribute' => 'headLastName'],
                ['attribute' => 'headRole'],
                ['attribute' => 'schTel'],
                ['attribute' => 'ntp_eligible'],
                ['attribute' => 'priority'],
                ['attribute' => 'censusDate'],
                ['attribute' => 'lastUpdated'],
            ],
            'maxEntriesPerFile' => $this->MAX_ENTRIES_PER_FILE,
            'resultConfig' => [
                'forceArchive' => true
            ],
        ]);
    }

    protected function attendeesQuery(string $eventType, string $site, string $start, string $end, string $priority, string $tag): string
    {
        $siteWhere = $site != '*' ? 'AND s.id = '.$site : '';
        $siteTag = strlen($tag) > 0 ? 'AND e.id IN(
            SELECT distinct(sourceId)
                FROM content c
                INNER JOIN elements em
                ON em.id = c.elementId
                INNER JOIN relations r
                ON c.elementId = r.targetId
                WHERE c.title = "'.$tag.'"
        )' : '';
        $prior = $priority == 'prior' ? 'AND priority = "1"' : '';

        return '
            SELECT handle AS RSN, elementId as eventID, title AS training, orgName, priority, orgUrn, postCode, a.name as name, email, jobRole, days AS modulesAttended, newsletter AS mailingList, anonymous,
                (case WHEN e.typeId = 16 then "online"
                    WHEN e.typeId = 17 then "location"
                    ELSE "hybrid"
                    END) AS eventType
                FROM entries e
                INNER JOIN content c ON e.id = c.elementId
                INNER JOIN sites s ON c.siteId = s.id
                INNER JOIN attendees_attendees a ON e.id = a.eventId

                WHERE e.`id` IN (

                    SELECT eventId FROM
                    (
                        SELECT eventId, RSN, Training, MAX(lastTrainingDate) AS lastTrainingDate FROM
                        (
                            SELECT e.id AS eventId, s.handle AS RSN, c.title AS training,
                            CASE
                            WHEN d1.field_eventDate_startDateTime IS NOT NULL THEN DATE_FORMAT(d1.field_eventDate_startDateTime, "%d-%m-%Y")
                            WHEN d2.field_eventDate_startDateTime IS NOT NULL THEN DATE_FORMAT(d2.field_eventDate_startDateTime, "%d-%m-%Y")
                            ELSE DATE_FORMAT(d3.field_eventDate_startDateTime, "%d-%m-%Y") END AS lastTrainingDate
                            FROM entries e
                                INNER JOIN matrixblocks m ON m.ownerId = e.id
                                INNER JOIN content c ON e.id = c.elementId
                                INNER JOIN elements em ON e.id = em.id
                                INNER JOIN sites s on s.id = c.siteId
                                LEFT JOIN matrixcontent_eventdatestime d1 ON d1.elementId = m.id
                                LEFT JOIN matrixcontent_eventdatestimeonline d2 ON d2.elementId = m.id
                                LEFT JOIN matrixcontent_eventdatestime d3 ON d3.elementId = m.id
                            WHERE e.sectionId = 15
                            AND e.typeId IN ('.$eventType.')
                            '.$siteWhere.'
                            AND em.revisionId IS NULL
                            AND em.draftId IS NULL
                            AND e.authorId IS NOT NULL
                            AND e.postDate IS NOT NULL
                            '.$siteTag.'
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
                            ORDER BY RSN ASC, lastTrainingDate DESC
                        ) as results
                        GROUP BY eventId, RSN, Training
                        ORDER BY RSN ASC, lastTrainingDate DESC
                    ) as export
                )
                '.$prior.'
                ORDER BY RSN ASC, eventID ASC
        ';
    }

    protected function attendeeSchoolsQuery(string $eventType, string $site, string $start, string $end, string $priority, string $tag): string
    {
        $siteWhere = $site != '*' ? 'AND s.id = '.$site : '';
        $siteTag = $tag !== '' ? 'AND e.id IN(
            SELECT distinct(sourceId)
                FROM content c
                INNER JOIN elements em
                ON em.id = c.elementId
                INNER JOIN relations r
                ON c.elementId = r.targetId
                WHERE c.title = "'.$tag.'"
        )' : '';
        $prior = $priority == 'prior' ? 'AND priority = "1"' : '';

        return '
            SELECT distinct(urn), eventId, rsn, training, days FROM
            (
                SELECT
                    distinct(a.eventId) AS eventId,
                    s.name AS rsn,
                    c.title AS training,
                    a.days AS days,
                    a.orgUrn AS urn
                        FROM attendees_attendees a
                    INNER JOIN content c ON a.eventId = c.elementId
                    INNER JOIN sites s on s.id = c.siteId
                    WHERE
                    eventId IN
                    (
                        SELECT
                        DISTINCT(eventId) AS ids
                        FROM
                        (
                            SELECT e.id AS eventId
                                FROM entries e
                                INNER JOIN matrixblocks m ON m.ownerId = e.id
                                INNER JOIN content c ON e.id = c.elementId
                                INNER JOIN elements em ON e.id = em.id
                                INNER JOIN sites s on s.id = c.siteId
                                LEFT JOIN matrixcontent_eventdatestime d1 ON d1.elementId = m.id
                                LEFT JOIN matrixcontent_eventdatestimeonline d2 ON d2.elementId = m.id
                                LEFT JOIN matrixcontent_eventdatestime d3 ON d3.elementId = m.id
                                    WHERE e.sectionId = 15
                                    AND e.typeId IN ('.$eventType.')
                                    '.$siteWhere.'
                                    AND em.revisionId IS NULL
                                    AND em.draftId IS NULL
                                    AND e.authorId IS NOT NULL
                                    AND e.postDate IS NOT NULL
                                    '.$siteTag.'
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
                                        WHERE eventDate BETWEEN "' . $start . '" AND  "' . $end . '"
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
                                                    )
                                                    AS c
                                                    GROUP BY c.elementId, field_eventDate_startDateTime
                                                ) AS cf
                                                WHERE eventDate BETWEEN "' . $start . '" AND  "' . $end . '"
                                    )


                        )
                        AS res
                    )
                    '.$prior.'
                    ORDER BY RSN ASC, training ASC, days DESC, URN ASC)
                    AS sortedRes
        ';
    }

    protected function eventsQuery(string $eventType, string $site, string $start, string $end, string $priority, string $tag): string
    {
        $siteWhere = $site != '*' ? 'AND s.id = '.$site : '';
        $siteTag = $tag !== '' ? 'AND e.id IN(
            SELECT distinct(sourceId)
                FROM content c
                INNER JOIN elements em
                ON em.id = c.elementId
                INNER JOIN relations r
                ON c.elementId = r.targetId
                WHERE c.title = "'.$tag.'"
        )' : '';
        $prior = $priority == 'prior' ? 'WHERE a.priority = "1"' : '';

        return '
            SELECT eventId, RSN, Training, MAX(lastTrainingDate) AS lastTrainingDate FROM
            (
                SELECT e.id AS eventId, s.handle AS RSN, c.title AS training,
                    CASE
                    WHEN d1.field_eventDate_startDateTime IS NOT NULL THEN DATE_FORMAT(d1.field_eventDate_startDateTime, "%d-%m-%Y")
                    WHEN d2.field_eventDate_startDateTime IS NOT NULL THEN DATE_FORMAT(d2.field_eventDate_startDateTime, "%d-%m-%Y")
                        ELSE DATE_FORMAT(d3.field_eventDate_startDateTime, "%d-%m-%Y") END
                    AS lastTrainingDate
                    FROM entries e
                        INNER JOIN matrixblocks m ON m.ownerId = e.id
                        INNER JOIN content c ON e.id = c.elementId
                        INNER JOIN elements em ON e.id = em.id
                        INNER JOIN sites s ON s.id = c.siteId
                        LEFT JOIN matrixcontent_eventdatestime d1 ON d1.elementId = m.id
                        LEFT JOIN matrixcontent_eventdatestimeonline d2 ON d2.elementId = m.id
                        LEFT JOIN matrixcontent_eventdatestime d3 ON d3.elementId = m.id
                            WHERE e.sectionId = 15
                            AND e.typeId IN ('.$eventType.')
                            AND em.revisionId IS NULL
                            AND em.draftId IS NULL
                            AND e.authorId IS NOT NULL
                            AND e.postDate IS NOT NULL
                            '.$siteTag.'
                            '.$siteWhere.'
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
                                    )AS a
                                    GROUP BY a.elementId, field_eventDate_startDateTime
                                ) AS af
                                WHERE eventDate BETWEEN "' . $start . '" AND  "' . $end . '"
                                UNION
                                    SELECT  elementId FROM
                                    (
                                        SELECT elementId, field_eventDate_startDateTime AS eventDate FROM
                                        (
                                            SELECT elementId, field_eventDate_startDateTime FROM matrixcontent_eventdatestimeonline m
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
                            ORDER BY RSN ASC, lastTrainingDate DESC
            ) as results
            GROUP BY eventId, RSN, Training
            ORDER BY RSN ASC, lastTrainingDate DESC
        ';
    }

    private function _group_by($array, $key) {
        $return = array();
        foreach($array as $val) {
            $return[$val[$key]][] = $val;
        }
        return $return;
    }
}
