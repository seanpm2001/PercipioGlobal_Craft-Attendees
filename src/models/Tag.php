<?php

namespace percipiolondon\attendees\models;

use craft\base\Model;

class Tag extends Model
{
    public $tag;

    protected function defineRules(): array
    {
        $rules = parent::defineRules();
        $rules[] = ['tag', 'required'];
        $rules[] = ['tag', 'string'];

        return $rules;
    }
}
