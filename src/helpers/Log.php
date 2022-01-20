<?php

namespace percipiolondon\attendees\helpers;

use percipiolondon\attendees\records\Logs;


class Log
{

    public static function error( string $message, string $eventId, string $filename, string $filepath, string $line, string $name): bool
    {
        $log = new Logs();

        $log->message =$message ?? '';
        $log->eventId = $eventId ?? '';
        $log->filename = $filename ?? '';
        $log->filepath = $filepath ?? '';
        $log->line = $line ?? '';
        $log->attendee = $name ?? '';

        return $log->save(true);
    }
}
