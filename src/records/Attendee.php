<?php

namespace percipiolondon\attendees\records;

use Craft;

use percipiolondon\attendees\db\Table;
use yii\db\ActiveRecord;
use yii\validators\Validator;

/**
 * @property int $siteId;
 * @property string $name;
 * @property string $email;
 * @property string $jobRole;
 * @property int $days;
 * @property bool $newsletter;
 * @property string $orgName;
 * @property int|null $orgUrn;
 * @property string|null $postCode;
 * @property bool $priority;
 * @property int $eventId;
 * @property bool $approved;
 * @property string|null $identifier;
 * @property string $anonymous;
 **/

class Attendee extends ActiveRecord
{
    public static function tableName()
    {
        return Table::ATTENDEES;
    }

    public function rules()
    {
        $rules = parent::rules();

        $rules[] = [['days','eventId', 'priority'], 'required'];
        $rules[] = [['orgName', 'name', 'jobRole', 'email'], 'trim'];
//        $rules[] = [['orgName', 'name', 'jobRole', 'email'], 'filter', 'filter' => function ($value) {
//            return filter_var(htmlspecialchars($value, ENT_QUOTES), FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_NO_ENCODE_QUOTES);
//        }];
        $rules[] = [['orgName', 'name', 'jobRole'], 'required', 'when' => function($model){
            return $model->anonymous === 0;
        }];
        $rules[] = [['postCode'], 'string', 'max' => 10];

        return $rules;
    }
}
