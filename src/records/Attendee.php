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
        $rules[] = [['orgName','name','jobRole'], 'required', 'when' => function($model){
            return $model->anonymous === 0;
        }];
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
