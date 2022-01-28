<?php

namespace percipiolondon\attendees\helpers;

use Twig\Extension\AbstractExtension;

class EventSortExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return array(
            new \Twig\TwigFunction('sort', array(
                $this,
                'sort'
            ))
        );
    }

    function sort($events, $period){

        if(strlen($period) > 1){
            $before = date('U', strtotime('31 july ' . (int)$period ));
            $after = date('U', strtotime('01 september ' . ((int)$period - 1) ));
        }else{
            $before = date("U");
            $after = date("U", strtotime('-' . $period . ' Months'));
        }

        $unique = $this->_returnUniqueProperty($this->sortEvents($events, $before, $after), 'id');

        return array_column($unique, 'id');
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

    private static function _returnUniqueProperty($array, $property)
    {
        $tempArray = array_unique(array_column($array, $property));
        return array_values(array_intersect_key($array, $tempArray));
    }
}
