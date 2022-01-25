<?php

namespace percipiolondon\attendees\migrations;

use Craft;
use craft\db\Migration;
use percipiolondon\attendees\db\Table;

/**
 * m220125_124021_schoolpriority migration.
 */
class m220125_124021_schoolpriority extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        // Place migration code here...
        $this->addColumn(Table::ATTENDEES, 'priority', $this->boolean());
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn(Table::ATTENDEES, 'priority');
        return false;
    }
}
