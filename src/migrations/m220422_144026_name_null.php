<?php

namespace percipiolondon\attendees\migrations;

use Craft;
use craft\db\Migration;
use percipiolondon\attendees\db\Table;

/**
 * m220422_144026_name_null migration.
 */
class m220422_144026_name_null extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        // Place migration code here...
        $this->alterColumn(Table::ATTENDEES, 'name', $this->string(255));
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m220422_144026_name_null cannot be reverted.\n";
        return false;
    }
}
