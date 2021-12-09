<?php

namespace percipiolondon\craftattendees\migrations;

use Craft;
use craft\db\Migration;
use percipiolondon\craftattendees\db\Table;

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
        // Place uninstallation code here...
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
                    'postCode' => $this->string(),
                    'name' => $this->string(255)->notNull(),
                    'email' => $this->string(255)->notNull(),
                    'jobRole' => $this->string(255)->notNull(),
                    'days' => $this->integer()->notNull(),
                    'newsletter' => $this->boolean()->defaultValue(0),
                    'siteId' => $this->integer()->notNull(),
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
        $this->addForeignKey(null, Table::ATTENDEES, ['siteId'], '{{%sites}}', ['id'], 'CASCADE', 'CASCADE');
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
