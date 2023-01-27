<?php

namespace percipiolondon\attendees\models;

use craft\base\Model;
use craft\behaviors\EnvAttributeParserBehavior;

class Settings extends Model
{
    public $eventSection = '';
    public $pluginName = 'Attendees';
    public $csvStoragePath = '';
    public $slackWebhook = '';

    public function rules()
    {
        return [
            [['csvStoragePath', 'eventSection', 'slackWebhook'], 'required'],
            ['eventSection', 'string']
        ];
    }

    /**
     * @return array
     */
    public function behaviors(): array
    {
        // Keep any parent behaviors
        $behaviors = parent::behaviors();

        return array_merge($behaviors, [
            'parser' => [
                'class' => EnvAttributeParserBehavior::class,
                'attributes' => ['csvStoragePath', 'slackWebhook'],
            ],
        ]);
    }
}
