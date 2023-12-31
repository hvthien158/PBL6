<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends FormRequest
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
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|min:6',
            'department_id' => 'required|',
            'salary' => 'nullable',
            'position' => 'nullable',
            'role' => 'nullable',
        ];
    }
}
