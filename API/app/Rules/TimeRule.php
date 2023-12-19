<?php

namespace App\Rules;

use Carbon\Carbon;
use Closure;
use DateTimeZone;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Rule;

class TimeRule implements Rule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function passes($attribute, $value)
    {
        $timeCheckIn = request()->input('timeValidCheckIn');
        $timeCheckOut = request()->input('timeValidCheckOut');

        return $timeCheckIn < $timeCheckOut;
    }
    public function message()
    {
        return 'Thời gian check in phải nhỏ hơn thời gian check out.';
    }
}
