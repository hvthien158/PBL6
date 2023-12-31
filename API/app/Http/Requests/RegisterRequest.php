<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|confirmed|min:6',
            'department_id' => 'required',
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
