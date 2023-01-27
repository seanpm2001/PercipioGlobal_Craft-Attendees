<?php

namespace percipiolondon\attendees\console\controllers;

use craft\console\Controller;
use craft\helpers\App;
use percipiolondon\attendees\Attendees;

class CsvController extends Controller
{
    public $days = 60;

    public function options($actionID)
    {
        $options = parent::options($actionID);
        $options[] = 'days';

        return $options;
    }

    /**
     * Handles the deletion of CSV files older than the given period (dafault 60 days)
     *
     * @return mixed
     */
    public function actionDelete()
    {
        $files = glob(App::parseEnv(Attendees::$plugin->getSettings()->csvStoragePath).DIRECTORY_SEPARATOR.'*');
        $now = time();

        foreach ($files as $file) {
            if (is_file($file) && ($now - filemtime($file) >= 60 * 60 * 24 * $this->days)) {
                unlink($file);
            }
        }
    }
}
