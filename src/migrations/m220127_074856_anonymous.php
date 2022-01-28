<?php

namespace percipiolondon\attendees\migrations;

use Craft;
use craft\db\Migration;
use percipiolondon\attendees\db\Table;

/**
 * m220127_074856_anonymous migration.
 */
class m220127_074856_anonymous extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        // Place migration code here...
        $this->addColumn(Table::ATTENDEES, 'anonymous', $this->boolean());
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn(Table::ATTENDEES, 'anonymous');
        return false;
    }
}
