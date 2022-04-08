<?php

namespace percipiolondon\attendees\models;

use craft\base\Model;

class Export extends Model
{
    public $exportType;
    public $eventType;
    public $school;
    public $start;
    public $end;
    public $site;
    public $tag;

    protected function defineRules(): array
    {
        $rules = parent::defineRules();
        $rules[] = [['exportType', 'eventType', 'school', 'startDate', 'endDate', 'site'], 'required'];
        $rules[] = [['exportType', 'eventType', 'school', 'startDate', 'endDate', 'site', 'tag'], 'string'];

        return $rules;
    }
}
