<?php

namespace percipiolondon\attendees\migrations;

use Craft;
use craft\db\Migration;
use craft\helpers\MigrationHelper;

use percipiolondon\attendees\db\Table;

/**
 * Install migration.
 */
class Install extends Migration
{
    public $driver;

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->driver = Craft::$app->getConfig()->getDb()->driver;
        if ($this->createTables()) {
            $this->createIndexes();
            $this->addForeignKeys();
            // Refresh the db schema caches
            Craft::$app->db->schema->refresh();
            $this->insertDefaultData();
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
//        $this->driver = Craft::$app->getConfig()->getDb()->driver;
//        $this->dropForeignKeys();
//        $this->removeTables();

        return true;
    }





    // Protected Methods
    // =========================================================================

    /**
     * Creates the tables needed for the Records used by the plugin
     *
     * @return bool
     */
    protected function createTables()
    {
        $tablesCreated = false;

        //attendees table
        $tableSchema = Craft::$app->db->schema->getTableSchema(Table::ATTENDEES);
        if ($tableSchema === null) {
            $tablesCreated = true;
            $this->createTable(
                Table::ATTENDEES,
                [
                    'id' => $this->primaryKey(),
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime()->notNull(),
                    'uid' => $this->uid(),
                    'orgName' => $this->string(255)->notNull(),
                    'orgUrn' => $this->integer(),
                    'postCode' => $this->string(10),
                    'name' => $this->string(255),
                    'email' => $this->string(255)->notNull(),
                    'jobRole' => $this->string(255)->notNull(),
                    'days' => $this->integer()->notNull(),
                    'newsletter' => $this->boolean()->defaultValue(0),
                    'priority' => $this->boolean()->defaultValue(0),
                    'approved' => $this->boolean()->defaultValue(0),
                    'siteId' => $this->integer()->notNull(),
                    'eventId' => $this->integer()->notNull(),
                    'identifier' => $this->string(255),
                    'anonymous' => $this->boolean()->defaultValue(0),
                ]
            );
        }

        //follow on support table
        $tableSchema = Craft::$app->db->schema->getTableSchema(Table::FOLLOW_ON_SUPPORT);
        if ($tableSchema === null) {
            $tablesCreated = true;
            $this->createTable(
                Table::FOLLOW_ON_SUPPORT,
                [
                    'id' => $this->primaryKey(),
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime()->notNull(),
                    'uid' => $this->uid(),
                    'optionId' => $this->integer()->notNull(),
                    'eventId' => $this->integer()->notNull(),
                ]
            );
        }

        //follow on support options table
        $tableSchema = Craft::$app->db->schema->getTableSchema(Table::FOLLOW_ON_SUPPORT_OPTIONS);
        if ($tableSchema === null) {
            $tablesCreated = true;
            $this->createTable(
                Table::FOLLOW_ON_SUPPORT_OPTIONS,
                [
                    'id' => $this->primaryKey(),
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime()->notNull(),
                    'uid' => $this->uid(),
                    'name' => $this->string(255)->notNull(),
                    'value' => $this->string(255),
                ]
            );
        }

        //logs
        $tableSchema = Craft::$app->db->schema->getTableSchema(Table::LOGS);
        if ($tableSchema === null) {
            $tablesCreated = true;
            $this->createTable(
                Table::LOGS,
                [
                    'id' => $this->primaryKey(),
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime()->notNull(),
                    'message' => $this->string(255)->notNull(),
                    'eventId' => $this->integer()->notNull(),
                    'filepath' => $this->string(255)->notNull(),
                    'filename' => $this->string(255)->notNull(),
                    'line' => $this->integer()->notNull(),
                    'attendee' => $this->string(255)->notNull(),
                    'type' => $this->string(255)->notNull(),
                    'totalLines' => $this->integer()->notNull(),
                    'uid' => $this->uid(),
                ]
            );
        }

        return $tablesCreated;

    }

    protected function createIndexes()
    {
        $this->createIndex(null, Table::ATTENDEES, 'siteId', false);
    }

    protected function addForeignKeys()
    {
        $this->addForeignKey(null, Table::LOGS, ['eventId'], '{{%entries}}', ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::ATTENDEES, ['siteId'], '{{%sites}}', ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::ATTENDEES, ['eventId'], '{{%entries}}', ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::FOLLOW_ON_SUPPORT, ['optionId'], Table::FOLLOW_ON_SUPPORT_OPTIONS, ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::FOLLOW_ON_SUPPORT, ['eventId'], '{{%entries}}', ['id'], 'CASCADE', 'CASCADE');
    }

    protected function dropForeignKeys()
    {
        $tables = [
            Table::ATTENDEES,
            Table::FOLLOW_ON_SUPPORT_OPTIONS,
            Table::FOLLOW_ON_SUPPORT,
            Table::LOGS,
        ];
        foreach ($tables as $table) {
            $this->_dropForeignKeyToAndFromTable($table);
        }
    }

    protected function removeTables()
    {
        $this->dropTableIfExists(Table::ATTENDEES);
        $this->dropTableIfExists(Table::FOLLOW_ON_SUPPORT_OPTIONS);
        $this->dropTableIfExists(Table::FOLLOW_ON_SUPPORT);
        $this->dropTableIfExists(Table::LOGS);
    }

    protected function insertDefaultData()
    {
        $rows = [];

        $rows[] = ['Additional training day on specialism and/or implementation planning','opt0'];
        $rows[] = ['Coaching','opt1'];
        $rows[] = ['Coordinated planning with school for partnership offer','opt2'];
        $rows[] = ['ELE/SLE/NLE brokerage of visit','opt3'];
        $rows[] = ['ELE/SLE/NLE coaching model, multiple visits and support with independent auditing and evaluation','opt4'];
        $rows[] = ['Follow on email','opt5'];
        $rows[] = ['Follow on email series/webinar','opt6'];
        $rows[] = ['Follow up call (linked to activities)','opt7'];
        $rows[] = ['Free expert webinar','opt8'];
        $rows[] = ['Improvement partnership model involving a variety of inputs and supports','opt9'];
        $rows[] = ['Peer partnerships (develop a straightforward protocol and supports)','opt10'];
        $rows[] = ['Post card reminders','opt11'];
        $rows[] = ['Reciprocal peer visit','opt12'];
        $rows[] = ['Reduced fee for further training','opt13'];
        $rows[] = ['Resources to share with colleagues','opt14'];
        $rows[] = ['RSN visit','opt15'];
        $rows[] = ['School/Trust CPD programme','opt16'];
        $rows[] = ['Sign up to newsletters','opt17'];
        $rows[] = ['Supported online implementation programme','opt18'];
        $rows[] = ['Supported school CPD planning','opt19'];
        $rows[] = ['Webinar/online series','opt20'];
        $rows[] = ['Whole school training offer','opt21'];

        $this->batchInsert(Table::FOLLOW_ON_SUPPORT_OPTIONS, ['name', 'value'], $rows);
    }






    // Private Methods
    // =========================================================================
    /**
     * Returns if the table exists.
     *
     * @param string $tableName
     * @param \yii\db\Migration|null $migration
     * @return bool If the table exists.
     * @throws NotSupportedException
     */
    private function _tableExists(string $tableName): bool
    {
        $schema = $this->db->getSchema();
        $schema->refresh();
        $rawTableName = $schema->getRawTableName($tableName);
        $table = $schema->getTableSchema($rawTableName);

        return (bool)$table;
    }

    /**
     * @param string $tableName
     * @throws NotSupportedException
     */
    private function _dropForeignKeyToAndFromTable(string $tableName)
    {
        if ($this->_tableExists($tableName)) {
            MigrationHelper::dropAllForeignKeysToTable($tableName, $this);
            MigrationHelper::dropAllForeignKeysOnTable($tableName, $this);
        }
    }
}
