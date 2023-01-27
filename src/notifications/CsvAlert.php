<?php

namespace percipiolondon\notifications;

use Craft;
use craft\elements\Entry;
use craft\helpers\App;
use craft\helpers\ArrayHelper;
use craft\mail\Message;
use craft\records\Site;
use percipioglobal\notifications\messages\SlackMessage;
use percipioglobal\notifications\models\Notification;
use percipiolondon\attendees\Attendees;

class CsvAlert extends Notification
{
    public string $message = 'There was an error importing the CSV';
    public array|null $log = null;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($event = null)
    {
        parent::__construct($event);
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array
     */
    public function via()
    {
        return [
            'slack' => App::parseEnv(Attendees::$plugin->getSettings()->slackWebhook)
        ];
    }

    public function toSlack()
    {
        $slack = new SlackMessage();

        $slack->from('RSN Police');
        $slack->content((Craft::$app->getConfig()->getGeneral()->devMode ? 'Dev: ' : 'Production: ') . $this->message);

        if ($this->log) {
            $log = $this->log;
            $slack->attachment(function ($attachment) use ($log) {
                $event = Entry::find()
                    ->id($log['eventId'])
                    ->site('*')
                    ->anyStatus()
                    ->one();

                $school = Site::findOne(['id' => $event->siteId]);

                $attachment->title('Error for: '. ($log['name'] == '' ? 'Anonymous' : $log['name'])  . ' on "' . $event->title . '"')
                    ->fields([
                        'School' => $school->name,
                        'File' => $log['file'],
                        'File Path' => $log['filepath'],
                        'line' => $log['line'],
                    ]);
            });
        }

        return $slack;
    }
}
