<?php

namespace percipiolondon\attendees\helpers;

use percipiolondon\attendees\records\FollowOnSupportOptions;
use yii\web\NotFoundHttpException;
use yii\web\Request;
use Craft;

use percipiolondon\craftstaff\models\FollowOnSupport as FollowOnSupportModel;
use percipiolondon\attendees\records\FollowOnSupport as FollowOnSupportRecord;

class FollowOnSupport
{
    public static function populateFollowOnSupportFromPost( Request $request = null): AttendeeModel
    {
        if (!$request) {
            $request = Craft::$app->getRequest();
        }

        $value = $request->getBodyParam('value');
        $event = $request->getBodyParam('event');

        $option = FollowOnSupportOptions::find()->where(['value' => $value])->one();

        if($option){
            $entry = FollowOnSupportRecord::find()->where(['optionId' => $option->id, 'eventId' => $event])->one();

            if(!$entry){
                $entry = new FollowOnSupportModel();
            }else{

            }


        }


//        if(!$value){
//            $option = new FollowOnSupportModel();
//        }else{
//            $option = AttendeeModel::find()->id($attendeeId)->one();
//
//            if (!$option) {
//                throw new NotFoundHttpException(Craft::t('craft-attendees', 'No option with the value “{id}”', ['id' => $value]));
//            }
//        }
//
//        $option
//
//        return $attendee;
    }
}
