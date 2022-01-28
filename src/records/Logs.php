<?php

namespace percipiolondon\attendees\records;

use percipiolondon\attendees\db\Table;
use yii\db\ActiveRecord;

/**
 * @property string $type;
 * @property string $message;
 * @property string $eventId;
 * @property string $filename;
 * @property string $filepath;
 * @property string $line;
 * @property string $attendee;
 * @property string $totalLines;
 **/

class Logs extends ActiveRecord
{
    public static function tableName()
    {
        return Table::LOGS;
    }
}
