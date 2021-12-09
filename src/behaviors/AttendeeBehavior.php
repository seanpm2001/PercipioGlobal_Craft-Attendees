<?php

namespace percipiolondon\craftattendees\behaviors;

use percipiolondon\craftattendees\elements\Attendee;
use percipiolondon\craftattendees\elements\db\AttendeeQuery;
use yii\base\Behavior;

class AttendeeBehavior extends Behavior
{
    public function attendees($criteria = null): AttendeeQuery
    {
        $query = Attendee::find();

        if($criteria) {
            \Craft::configure($query, $criteria);
        }

        return $query;
    }
}