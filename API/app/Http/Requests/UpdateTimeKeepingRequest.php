<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTimeKeepingRequest extends FormRequest
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
            'user_id' => 'required',
            'date' => 'required',
            'time_check_in' => 'nullable',
            'time_check_out' => 'nullable',
            'status_am' => 'nullable|integer|between:0,2',
            'status_pm' => 'nullable|integer|between:0,2',
        ];
    }
}
