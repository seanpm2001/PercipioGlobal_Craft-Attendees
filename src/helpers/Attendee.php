<?php

namespace percipiolondon\attendees\helpers;

use yii\web\Request;
use Craft;

use percipiolondon\attendees\elements\Attendee as AttendeeModel;

class Attendee
{
    public static function populateAttendeeFromPost( Request $request = null, AttendeeModel $attendee = null): AttendeeModel
    {
        if (!$request) {
            $request = Craft::$app->getRequest();
        }

        if(!$attendee){
            $attendee = new AttendeeModel();
        }

        $attendee->orgName = $request->getBodyParam('orgName');
        $attendee->postCode = $request->getBodyParam('postCode');
        $attendee->name = $request->getBodyParam('name');
        $attendee->email = $request->getBodyParam('email');
        $attendee->jobRole = $request->getBodyParam('jobRole');
        $attendee->days = $request->getBodyParam('days');
        $attendee->newsletter = $request->getBodyParam('newsletter');
        $attendee->eventId = $request->getBodyParam('event');

        return $attendee;
    }
}
