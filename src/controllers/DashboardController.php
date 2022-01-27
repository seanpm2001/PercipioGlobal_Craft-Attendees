<?php

namespace percipiolondon\attendees\controllers;

use craft\web\Controller;
use percipiolondon\attendees\Attendees;
use percipiolondon\attendees\records\Attendee as AttendeeRecord;
use percipiolondon\attendees\records\FollowOnSupport;
use percipiolondon\attendees\records\FollowOnSupportOptions;

class DashboardController extends Controller
{
    public function actionFetchEvents(string $site = '*', string $period = '3')
    {
        $site = $site === 'main' ? '*' : $site;

        $events = \craft\elements\Entry::find()
            ->site($site)
            ->anyStatus()
            ->section(Attendees::getInstance()->settings->eventSection)
            ->all();

        $before = null;
        $after = null;

        if(strlen($period) > 1){
            $before = date('U', strtotime('31 july ' . (int)$period ));
            $after = date('U', strtotime('01 september ' . ((int)$period - 1) ));
        }else{
            $before = date("U");
            $after = date("U", strtotime('-' . $period . ' Months'));
        }

        $sorted = $this->sortEvents($events, $before, $after);
        $attendees = [];
        $followonsupport = [];

        foreach($sorted as $event){
            $evtAttendees = AttendeeRecord::find()
                ->where(['eventId' => $event->id])
                ->all();

            $evtFollow = FollowOnSupport::find()
                ->where(['eventId' => $event->id])
                ->all();

            array_push($attendees, ...$evtAttendees);
            array_push($followonsupport, ...$evtFollow);
        }


        return $this->asJson([
            "events" => $sorted,
            "attendees" => $attendees,
            "follow_on_support" => $followonsupport,
            "follow_on_support_options" => FollowOnSupportOptions::find()->all()
        ]);
    }

    private function sortEvents($events, $before, $after)
    {
        $sortedEvents = [];

        $i = 0;
        foreach ($events as $event) {
            $i++;
            $eventDates = null;

            switch ($event->type->handle)
            {
                case 'onlineEvent':
                    $eventDates = $event->eventDatesTimeOnline->all();
                    break;
                case 'hybridEvent':
                    $eventDates = $event->eventHybridDatesTime->all();
                    break;
                default:
                    $eventDates = $event->eventDatesTime->all();
            }

            $eventDays = $this->sortEventDates($eventDates);

            //sort date to get nearest first
            if (sizeOf($eventDays) > 0) {
                $startDate = $eventDays[0]->startDateTime->format('U') + $i;

                if($startDate < (int)$before && $startDate > (int)$after){
                    $sortedEvents[$startDate] = $event;
                }
            }
        }

        ksort($sortedEvents);

        return array_values($sortedEvents);
    }

    private function sortEventDates( $dates ) {

        $eventDays = [];
        $hasFutureEvents = false;

        foreach( $dates as $date ) {
            if($date->startDateTime && $date->startTime){

                $eventDate = strtotime($date->startDateTime->format('Y-m-d') . ' ' . $date->startTime->format('H:i:s')) ?? null;

                if( $eventDate < date('U') ) {
                    $eventDays[] = $date;
                }else{
                    $hasFutureEvents = true;
                }
            }
        }

        if(!$hasFutureEvents){
            usort($eventDays, function($a, $b) {
                return ($a->startDateTime < $b->startDateTime) ? -1 : 1;
            });
        }else{
            $eventDays = [];
        }

        return $eventDays;

    }
}
