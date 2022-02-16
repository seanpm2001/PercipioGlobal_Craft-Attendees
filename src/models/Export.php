<?php

namespace percipiolondon\attendees\models;

use craft\base\Model;

class Export extends Model
{
    public $type;
    public $school;
    public $start;
    public $end;
    public $site;
    public $tag;

    protected function defineRules(): array
    {
        $rules = parent::defineRules();
        $rules[] = [['type', 'school', 'startDate', 'endDate', 'site'], 'required'];
        $rules[] = [['type', 'school', 'startDate', 'endDate', 'site', 'tag'], 'string'];

        return $rules;
    }
}
