<?php

namespace App\Helpers;

use Illuminate\Support\Carbon;

class TimestampHelper
{
    /**
     * Format datetime to ISO 8601 with microseconds using Carbon's built-in method
     * Format: 2026-03-02T10:19:01.000000Z
     */
    public static function formatTimestamp($datetime)
    {
        if (!$datetime) {
            return null;
        }
        
        if (is_string($datetime)) {
            $datetime = Carbon::parse($datetime);
        }
        
        return $datetime->toISOString();
    }

    /**
     * Combine date and time strings into Y-m-d H:i:s timestamp
     */
    public static function combineDateTime($date, $time)
    {
        if (!$date || !$time) {
            return null;
        }
        
        $datetime = Carbon::parse($date . ' ' . $time);
        return $datetime->format('Y-m-d H:i:s');
    }
}
