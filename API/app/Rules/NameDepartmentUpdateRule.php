<?php

namespace App\Rules;

use App\Models\Department;
use Closure;
use Illuminate\Contracts\Validation\Rule;

class NameDepartmentUpdateRule implements Rule
{
    /**
     * @param mixed $attribute
     * @param mixed $value
     * 
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $department = Department::all();
        foreach ($department as $department) {
            if ($department->name == $value && !($department->id == request()->route("id"))) {
                return false;
            }
        }
        return true;
    }
    /**
     * @return string
     */
    public function message()
    {
        return 'Name already exists';
    }
}
