<?php

namespace percipiolondon\attendees\records;

use percipiolondon\attendees\db\Table;
use yii\db\ActiveRecord;

/**
 * @property string $name;
 * @property string $value;
 **/

class FollowOnSupportOptions extends ActiveRecord
{
    public static function tableName()
    {
        return Table::FOLLOW_ON_SUPPORT_OPTIONS;
    }
}
