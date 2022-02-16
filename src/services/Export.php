<?php
namespace percipiolondon\attendees\services;

use percipiolondon\attendees\Attendees;
use yii\base\Component;
use yii\data\ArrayDataProvider;
use yii2tech\csvgrid\CsvGrid;
use percipiolondon\attendees\records\Attendee as AttendeeRecord;
use percipiolondon\attendees\models\Export as ExportModel;
use Craft;

class Export extends Component
{
    public function export(ExportModel $exportModel): CsvGrid
    {
        switch($exportModel->type){
            case 'event':
                $exporter = $this->_buildEventExport($exportModel->start, $exportModel->end, $exportModel->site, $exportModel->tag);
                break;
            case 'subscriptions':
                $exporter = $this->_buildSubscriptionsExport($exportModel->start, $exportModel->end, $exportModel->site, $exportModel->tag);
                break;
            case 'school-attendee':
                $exporter = $this->_buildSchoolAttendeeExport($exportModel->start, $exportModel->end, $exportModel->site, $exportModel->tag);
                break;
            case 'school-unique':
                $exporter = $this->_buildSchoolUniqueExport($exportModel->start, $exportModel->end, $exportModel->site, $exportModel->tag);
                break;
            default:
                $exporter = $this->_buildAttendeesExport($exportModel->start, $exportModel->end, $exportModel->site, $exportModel->tag);
        }

        return $exporter;
    }

    private function _buildAttendeesExport(string $start, string $end, string $site, string $tag): CsvGrid
    {
        $connection = \Yii::$app->getDb();
        $sql = $this->attendeesQuery($site, $start, $end, $tag);
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
            'maxEntriesPerFile' => 2000, // limit max rows per single file
            'resultConfig' => [
                'forceArchive' => true
            ],
        ]);
    }

    private function _buildEventExport(string $start, string $end, string $site, string $tag): CsvGrid
    {
        $connection = \Yii::$app->getDb();
        $sql = $this->eventsQuery($site, $start, $end, $tag);
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
            'maxEntriesPerFile' => 2000, // limit max rows per single file
            'resultConfig' => [
                'forceArchive' => true
            ],
        ]);
    }

    private function _buildSubscriptionsExport(string $start, string $end, string $site, string $tag): CsvGrid
    {
        $connection = \Yii::$app->getDb();
        $sql = $this->attendeesQuery($site, $start, $end, $tag);
        $command = $connection->createCommand($sql);
        $results = $command->queryAll();

        $subscriptionResults = [];

        foreach($results as $result){
            if(strlen($result['email']) > 0 && $result['newsletter'] == '1') {
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
            'maxEntriesPerFile' => 2000, // limit max rows per single file
            'resultConfig' => [
                'forceArchive' => true
            ],
        ]);
    }

    private function _buildSchoolAttendeeExport(string $start, string $end, string $site, string $tag): CsvGrid
    {
        $connection = \Yii::$app->getDb();
        $sql = $this->attendeeSchoolsQuery($site, $start, $end, $tag);
        $command = $connection->createCommand($sql);
        $results = $command->queryAll();

        $urns = [];

        foreach($results as $result){
            $urns[] = $result['orgUrn'];
        }

        $response = Attendees::getInstance()->metaseed->attendeeSchools($urns);
        Craft::dd($response);

        $query = AttendeeRecord::find();
        return new CsvGrid([
            'query' => $query,
            'maxEntriesPerFile' => 2000, // limit max rows per single file
            'resultConfig' => [
                'forceArchive' => true
            ],
        ]);
    }

    private function _buildSchoolUniqueExport(string $start, string $end, string $site, string $tag): CsvGrid
    {
        $connection = \Yii::$app->getDb();
        $sql = $this->attendeeSchoolsQuery($site, $start, $end, $tag);
        $command = $connection->createCommand($sql);
        $results = $command->queryAll();

        $urns = [];

        foreach($results as $result){
            $urns[] = $result['orgUrn'];
        }

        $urns = array_unique($urns);

        $response = Attendees::getInstance()->metaseed->attendeeSchools($urns);
        Craft::dd($response);

        $query = AttendeeRecord::find();
        return new CsvGrid([
            'query' => $query,
            'maxEntriesPerFile' => 2000, // limit max rows per single file
            'resultConfig' => [
                'forceArchive' => true
            ],
        ]);
    }

    protected function attendeesQuery(string $site, string $start, string $end, string $tag): string
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

        return '
            SELECT handle AS RSN, elementId as eventID, title AS training, orgName, priority, orgUrn, postCode, a.name as name, email, jobRole, days AS modulesAttended, newsletter, anonymous
                FROM entries e
                INNER JOIN content c ON e.id = c.elementId
                INNER JOIN sites s ON c.siteId = s.id
                INNER JOIN attendees_attendees a ON e.id = a.eventId

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
                            ORDER BY RSN ASC, lastTraingingDate DESC
                        ) as results
                        GROUP BY eventId, RSN, Training
                        ORDER BY RSN ASC, lastTrainingDate DESC
                    ) as export
                )
                ORDER BY RSN ASC, eventID ASC
        ';
    }

    protected function attendeeSchoolsQuery(string $site, string $start, string $end, string $tag): string
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

        return '
            SELECT orgUrn
                FROM entries e
                INNER JOIN content c ON e.id = c.elementId
                INNER JOIN sites s ON c.siteId = s.id
                INNER JOIN attendees_attendees a ON e.id = a.eventId

                WHERE e.`id` IN (

                    SELECT eventId FROM
                    (
                        SELECT eventId, Training, MAX(lastTraingingDate) AS lastTrainingDate FROM
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
                            ORDER BY RSN ASC, lastTraingingDate DESC
                        ) as results
                        GROUP BY eventId, Training
                        ORDER BY lastTrainingDate DESC
                    ) as export
                )
                AND priority = "1"
                ORDER BY eventID ASC
        ';
    }

    protected function eventsQuery(string $site, string $start, string $end, string $tag): string
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

        return '
            SELECT eventId, RSN, Training, MAX(lastTraingingDate) AS lastTrainingDate FROM
            (
                SELECT e.id AS eventId, s.handle AS RSN, c.title AS training,
                    CASE
                    WHEN d1.field_eventDate_startDateTime IS NOT NULL THEN DATE_FORMAT(d1.field_eventDate_startDateTime, "%d-%m-%Y")
                    WHEN d2.field_eventDate_startDateTime IS NOT NULL THEN DATE_FORMAT(d2.field_eventDate_startDateTime, "%d-%m-%Y")
                        ELSE DATE_FORMAT(d3.field_eventDate_startDateTime, "%d-%m-%Y") END
                    AS lastTraingingDate
                    FROM entries e
                        INNER JOIN matrixblocks m ON m.ownerId = e.id
                        INNER JOIN content c ON e.id = c.elementId
                        INNER JOIN elements em ON e.id = em.id
                        INNER JOIN sites s ON s.id = c.siteId
                        LEFT JOIN matrixcontent_eventdatestime d1 ON d1.elementId = m.id
                        LEFT JOIN matrixcontent_eventdatestimeonline d2 ON d2.elementId = m.id
                        LEFT JOIN matrixcontent_eventdatestime d3 ON d3.elementId = m.id
                            WHERE e.sectionId = 15
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
                            ORDER BY RSN ASC, lastTraingingDate DESC
            ) as results
            GROUP BY eventId, RSN, Training
            ORDER BY RSN ASC, lastTrainingDate DESC
        ';
    }
}
