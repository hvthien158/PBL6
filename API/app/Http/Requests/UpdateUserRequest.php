<?php

namespace App\Http\Requests;

use App\Rules\EmailUpdateRule;
use Illuminate\Foundation\Http\FormRequest;
use PharIo\Manifest\Rule;
class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "name"=> "required|string|between:4,100",
            'email' => ['required','string','email','max:100', new EmailUpdateRule],
            'department_id' => 'required|',
            'address' => 'string|nullable',
            'DOB' => 'nullable|date',
            'phone_number' => 'nullable',
            'avatar' => 'nullable',
            'salary' => 'nullable',
            'position' => 'nullable',
            'role' => 'nullable',
        ];
    }
}
