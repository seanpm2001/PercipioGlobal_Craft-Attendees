<?php

namespace percipiolondon\attendees\models;

use craft\base\Model;

class FollowOnSupport extends Model
{
    /**
     * @var string|null Name
     */
    public $optionId;
    public $eventId;

    protected function defineRules(): array
    {
        $rules = parent::defineRules();
        $rules[] = [['optionId', 'eventId'], 'required'];

        return $rules;
    }
}
