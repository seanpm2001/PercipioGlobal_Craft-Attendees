<?php

namespace percipiolondon\attendees\helpers;

use percipiolondon\attendees\Attendees;
use yii\web\NotFoundHttpException;
use yii\web\Request;
use Craft;

use percipiolondon\attendees\records\Attendee as AttendeeModel;

class Attendee
{
    public static function populateAttendeeFromPost( Request $request = null): AttendeeModel
    {
        if (!$request) {
            $request = Craft::$app->getRequest();
        }

        $attendeeId = $request->getBodyParam('attendeeId');

        if(!$attendeeId){
            $attendee = new AttendeeModel();
        }else{
            $attendee = AttendeeModel::findOne(['id' =>$attendeeId ]);

            if (!$attendee) {
                throw new NotFoundHttpException(Craft::t('craft-attendees', 'No attendee with the ID “{id}”', ['id' => $attendeeId]));
            }
        }

        $attendee->orgName = htmlspecialchars($request->getBodyParam('orgName'));
        $attendee->orgUrn = htmlspecialchars($request->getBodyParam('orgUrn'));
        $attendee->postCode = htmlspecialchars($request->getBodyParam('postCode'));
        $attendee->name = htmlspecialchars(trim($request->getBodyParam('name')));
        $attendee->email = htmlspecialchars(trim($request->getBodyParam('email')));
        $attendee->jobRole = htmlspecialchars($request->getBodyParam('jobRole'));
        $attendee->days = $request->getBodyParam('days') ?? 1;
        $attendee->newsletter = $request->getBodyParam('newsletter') ?? 0;
        $attendee->approved = $request->getBodyParam('approved') ?? 0;
        $attendee->anonymous = $request->getBodyParam('anonymous') ?? 0;
        $attendee->priority = $request->getBodyParam('priority') ?? 0;
        $attendee->eventId = htmlspecialchars($request->getBodyParam('event'));
        $attendee->siteId = htmlspecialchars($request->getBodyParam('siteId'));

        return $attendee;
    }

    public static function populateAttendeeFromArray( array $entry, string $identifier): AttendeeModel
    {
        $attendee = new AttendeeModel();
        $csvOrgUrn = is_numeric($entry['orgUrn'] ?? null) ? (int)$entry['orgUrn'] : null;

        if($csvOrgUrn){
            $result = Attendees::getInstance()->metaseed->school($csvOrgUrn);

            if($result->suggestions[0]->data ?? null){
                $attendee->orgName = $result->suggestions[0]->value ?? $entry['orgName'] ?? '';
                $attendee->postCode = $result->suggestions[0]->data->postcode ?? $entry['postCode'] ?? '';
                $attendee->priority = $result->suggestions[0]->data->priority ?? $entry['priority'] ?? 0;
                $attendee->approved = 1;
            }else{
                $attendee->orgName = $entry['orgName'] ?? '';
                $attendee->postCode = $entry['postCode'] ?? '';
                $attendee->priority = $entry['priority'] ?? 0;
                $attendee->approved = 0;
            }
        } else {
            $attendee->orgName = $entry['orgName'] ?? '';
            $attendee->postCode = $entry['postCode'] ?? '';
            $attendee->priority = $entry['priority'] ?? 0;
            $attendee->approved = 0;
        }

        $attendee->orgUrn = $entry['orgUrn'] ?? '';
        $attendee->name = trim($entry['name']) ?? '';
        $attendee->email = trim($entry['email']) ?? '';
        $attendee->jobRole = $entry['jobRole'] ?? '';
        $attendee->days = $entry['days'] ?? 1;
        $attendee->newsletter = str_contains($entry['newsletter'] ?? 'n', 'y');
        $attendee->eventId = $entry['event'] ?? '';
        $attendee->siteId = $entry['site'] ?? '';
        $attendee->anonymous = 0;
        $attendee->identifier = $identifier ?? '';

        return $attendee;
    }
}
