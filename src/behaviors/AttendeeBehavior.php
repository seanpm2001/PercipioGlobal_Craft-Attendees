<?php

namespace percipiolondon\attendees\behaviors;

use percipiolondon\attendees\elements\Attendee;
use percipiolondon\attendees\elements\db\AttendeeQuery;
use yii\base\Behavior;

class AttendeeBehavior extends Behavior
{
    public function attendees($criteria = null): AttendeeQuery
    {
        $query = Attendee::find();


        if($criteria) {
            $query->where(['eventId' => $criteria]);
        }

        return $query;
    }
}
