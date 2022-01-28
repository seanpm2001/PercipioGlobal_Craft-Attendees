<?php

namespace percipiolondon\attendees\helpers;

use percipiolondon\attendees\records\Logs;


class Log
{

    public static function log(array $log): bool
    {
        $entry = new Logs();

        $entry->message = $log['message'] ?? '';
        $entry->type = $log['status'] ?? 'error';
        $entry->eventId = $log['eventId'] ?? '';
        $entry->filepath = $log['filepath'] ?? '';
        $entry->filename = $log['file'] ?? '';
        $entry->line = $log['line'] ?? 0;
        $entry->attendee = $log['name'] ?? '';
        $entry->totalLines = $log['totalLines'] ?? 0;

        return $entry->save(true);
    }
}
