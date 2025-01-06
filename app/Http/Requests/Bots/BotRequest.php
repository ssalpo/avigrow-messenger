<?php

namespace App\Http\Requests\Bots;

use App\Enums\BotTypes;
use Illuminate\Foundation\Http\FormRequest;

class BotRequest extends FormRequest
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
            'company_id' => 'required|integer',
            'name' => 'required|string|max:255',
            'type' => 'required|numeric|in:' . (BotTypes::values()->implode(',')),
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'company_id' => auth()->user()->myCompany->id,
        ]);
    }
}
