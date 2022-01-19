<?php

namespace percipiolondon\attendees\jobs;

use craft\queue\BaseJob;
use percipiolondon\attendees\Attendees;

class CreateAttendeeJob extends BaseJob
{
    public $config;

    public function execute($queue)
    {
        Attendees::info('Importing row: ' . print_r($this->config, true));
//        \Craft::debug('Importing row: ' . print_r($this->config, true), __METHOD__);

    }

    /**
     * @inheritdoc
     */
    protected function defaultDescription(): string
    {
        return \Craft::t('craft-attendees', 'Save attendee - {name} ({org})', ['name' => $this->config['name'], 'org' => $this->config['orgName']]);
    }
}
