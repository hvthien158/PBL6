<?php

namespace App\Common;

use Carbon\Carbon;

class TimeByShift
{
    const MORNING = [
        'start' => '08:30:01',
        'end' => '11:45:01',
    ];
    const AFTERNOON = [
        'start' => '13:00:01',
        'end' => '17:45:01',
    ];

    public static function carbonize($time){
        return Carbon::createFromFormat('H:i:s', $time);
    }
}
