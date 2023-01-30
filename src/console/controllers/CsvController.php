<?php

namespace percipiolondon\attendees\console\controllers;

use craft\console\Controller;
use craft\helpers\App;
use percipiolondon\attendees\Attendees;

class CsvController extends Controller
{
    public int $maxAge = 5184000; //in seconds, default --> 60 days

    /**
     * Handles the deletion of CSV files older than the given period (dafault 60 days)
     *
     * @return mixed
     */
    public function actionDelete()
    {
        $files = glob(App::parseEnv(Attendees::$plugin->getSettings()->csvStoragePath).DIRECTORY_SEPARATOR.'*');

        $limit = time() - $this->maxAge;

        foreach ($files as $file) {
            if (is_file($file) && (filemtime($file) < $limit)) {
                unlink($file);
            }
        }
    }
}
