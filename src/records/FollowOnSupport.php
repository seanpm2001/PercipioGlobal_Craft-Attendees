<?php

namespace percipiolondon\attendees\records;

use percipiolondon\attendees\db\Table;
use yii\db\ActiveRecord;

/**
 * @property string $optionId;
 * @property string $eventId;
 **/

class FollowOnSupport extends ActiveRecord
{
    public static function tableName()
    {
        return Table::FOLLOW_ON_SUPPORT;
    }
}
