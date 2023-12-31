<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateDepartmentRequest extends FormRequest
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
            'departmentName' => 'required|string|unique:departments,department_name',
            'address'=> 'required|string',
            'email' =>'nullable|email',
            'phoneNumber' => 'nullable|string'
        ];
    }
    /**
     * @return array
     */
    public function messages(): array 
    {
        return [
            'departmentName.unique' => 'Name was exists'
        ];
    }
}
