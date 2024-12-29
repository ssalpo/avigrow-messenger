<?php

namespace App\Http\Requests\Bots;

use App\Enums\BotQuizAnswerTypes;
use Illuminate\Foundation\Http\FormRequest;

class BotQuizRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'content' => 'required|string|max:1000',
            'answer_type' => 'required|in:' . (BotQuizAnswerTypes::values()->implode(',')),
            'option_keyword' => 'nullable|string|max:255',
            'options' => 'nullable|array|required_if:answer_type,' . BotQuizAnswerTypes::OPTIONS->value,
            'options.*' => 'string|max:255'
        ];
    }

    protected function prepareForValidation(): void
    {
        $options = (array)$this->options;

        if ($this->option_keyword) {
            $options[] = $this->option_keyword;
        }

        $this->merge([
            'option_keyword' => null,
            'options' => $options,
        ]);
    }
}
