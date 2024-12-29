<?php

namespace App\Http\Requests\Bots;

use Illuminate\Foundation\Http\FormRequest;

class BotTriggerRequest extends FormRequest
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
            'keyword' => 'nullable|string|max:255',
            'keywords' => 'required|array',
            'keywords.*' => 'string|max:255',
            'response' => 'required|string|max:1000',
            'delay' => 'nullable|numeric|in:0,5,15,30,60,180,300,600,1800',
        ];
    }

    protected function prepareForValidation(): void
    {
        $keywords = (array)$this->keywords;

        if ($this->keyword) {
            $keywords[] = $this->keyword;
        }

        $this->merge([
            'keyword' => null,
            'keywords' => $keywords,
        ]);
    }
}
