<?php

namespace App\Rules;

use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\Rule;

class EmailUpdateRule implements Rule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function passes($attribute, $value)
    {
        $user = User::all();

        foreach ($user as $user) {
            if ($user->email === $value && request()->route('user')->id != $user->id) {
                return false;
            }
        }
        return true;
    }
    public function message()
    {
        return 'Email already exist';
    }
}
