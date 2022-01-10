<?php

namespace percipiolondon\attendees\migrations;

use Craft;
use craft\db\Migration;
use craft\helpers\MigrationHelper;
use percipiolondon\attendees\db\Table;
use percipiolondon\attendees\elements\Attendee;

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
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->driver = Craft::$app->getConfig()->getDb()->driver;
        $this->dropForeignKeys();
        $this->removeTables();

        $this->delete(\craft\db\Table::ELEMENTINDEXSETTINGS, ['type' => [ Attendee::class ]]);

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
        //attendees table
        $tableSchema = Craft::$app->db->schema->getTableSchema(Table::ATTENDEES);
        if ($tableSchema === null) {
            $this->createTable(
                Table::ATTENDEES,
                [
                    'id' => $this->integer()->notNull(),
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime()->notNull(),
                    'uid' => $this->uid(),
                    'orgName' => $this->string(255)->notNull(),
                    'orgUrn' => $this->integer(),
                    'postCode' => $this->string(10),
                    'name' => $this->string(255)->notNull(),
                    'email' => $this->string(255)->notNull(),
                    'jobRole' => $this->string(255)->notNull(),
                    'days' => $this->integer()->notNull(),
                    'newsletter' => $this->boolean()->defaultValue(0),
                    'approved' => $this->boolean()->defaultValue(0),
                    'siteId' => $this->integer()->notNull(),
                    'eventId' => $this->integer()->notNull(),
                    'PRIMARY KEY(id)',
                ]
            );
        }
    }

    protected function createIndexes()
    {
        $this->createIndex(null, Table::ATTENDEES, 'siteId', false);
    }

    protected function addForeignKeys()
    {
        $this->addForeignKey(null, Table::ATTENDEES, ['id'], '{{%elements}}', ['id'], 'CASCADE');
        $this->addForeignKey(null, Table::ATTENDEES, ['siteId'], '{{%sites}}', ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::ATTENDEES, ['eventId'], '{{%entries}}', ['id'], 'CASCADE', 'CASCADE');
    }

    protected function dropForeignKeys()
    {
        $tables = [
            Table::ATTENDEES,
        ];
        foreach ($tables as $table) {
            $this->_dropForeignKeyToAndFromTable($table);
        }
    }

    protected function removeTables()
    {
        $this->dropTableIfExists(Table::ATTENDEES);
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
