<?php

namespace percipiolondon\attendees\behaviors;

use percipiolondon\attendees\records\Attendee;
use yii\base\Behavior;
use yii\db\ActiveQuery;

class AttendeeBehavior extends Behavior
{
    public function attendees($criteria = null): ActiveQuery
    {
        $query = Attendee::find();

        if($criteria) {
            $query->where(['eventId' => $criteria]);
        }

        return $query;
    }
}
