<?php

namespace percipiolondon\craftattendees\records;

use percipiolondon\craftattendees\db\Table;
use yii\db\ActiveRecord;

/**
 * @property int $siteId;
 * @property string $name;
 * @property string $email;
 * @property string $jobRole;
 * @property string $days;
 * @property string $newsletter;
 * @property string $orgName;
 * @property string $postCode;
 **/

class Attendee extends ActiveRecord
{
    public static function tableName()
    {
        return Table::ATTENDEES;
    }
}