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
 * @property string $days;
 * @property string $newsletter;
 * @property string $orgName;
 * @property string $orgUrn;
 * @property string $postCode;
 * @property string $priority;
 * @property string $eventId;
 * @property string $approved;
 * @property string $identifier;
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

        $rules[] = [['orgName','name','jobRole','days','eventId'], 'required'];
        $rules[] = ['email', function($attribute, $params, Validator $validator){
            $preg = "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/i";

            // Valid email
            if (!preg_match($preg, $this->$attribute)) {
                $error = Craft::t('craft-attendees', '"{value}" is not a valid email address.', [
                    'attribute' => $attribute,
                    'value' => $this->$attribute,
                ]);

                $validator->addError($this, $attribute, $error);
            }
        }];

        return $rules;
    }
}
