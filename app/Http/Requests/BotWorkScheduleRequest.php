<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BotWorkScheduleRequest extends FormRequest
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
            'day_of_week' => 'required|in:Mon,Tue,Wed,Thu,Fri,Sat,Sun',
            'time_slots' => 'required|array',
            'time_slots.*' => 'required|date_format:H:i',
        ];
    }
}
