<?php

namespace App\Helpers;

use Carbon\Carbon;

class DateHelper
{
    /**
     * Safely format a date value (string or Carbon instance)
     *
     * @param mixed $date
     * @param string $format
     * @param string $default
     * @return string
     */
    public static function format($date, $format = 'd/m/Y', $default = '-')
    {
        if (empty($date)) {
            return $default;
        }
        
        if ($date instanceof Carbon) {
            return $date->format($format);
        }
        
        if (is_string($date)) {
            try {
                return Carbon::parse($date)->format($format);
            } catch (\Exception $e) {
                return $default;
            }
        }
        
        return $default;
    }
}

