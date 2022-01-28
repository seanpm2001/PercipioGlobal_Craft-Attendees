<?php

namespace percipiolondon\attendees\models;

use craft\base\Model;

class Settings extends Model
{
    public $eventSection = '';
    public $pluginName = 'Attendees';

    public function rules()
    {
        return [
            ['eventSection', 'string']
        ];
    }
}