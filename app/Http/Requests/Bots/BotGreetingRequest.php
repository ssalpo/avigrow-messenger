<?php

namespace App\Http\Requests\Bots;

use Illuminate\Foundation\Http\FormRequest;

class BotGreetingRequest extends FormRequest
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
            'template' => 'required|string|max:255',
            'delay' => 'nullable|numeric|in:0,5,15,30,60,180,300,600,1800',
        ];
    }
}
