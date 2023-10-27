<?php

namespace App\Rules;

use Carbon\Carbon;
use Closure;
use DateTimeZone;
use Illuminate\Contracts\Validation\ValidationRule;

class TimeRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $timezone = 'Asia/Ho_Chi_Minh';
        $checkTime = Carbon::now(new DateTimeZone($timezone));
        $today = Carbon::now(new DateTimeZone($timezone));
        $value = Carbon::createFromFormat('Y-m-d H:i:s', $value, $timezone);
        $value->setTimezone($timezone);
        if (!$value->isSameDay($today->startOfDay())) {
            $fail("The $attribute must be today's date ");
        }
        if (!$value->isBefore($checkTime)) {
            $fail("The $attribute must be before current time");
        }
    }
}
