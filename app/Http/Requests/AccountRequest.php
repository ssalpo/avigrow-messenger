<?php

namespace App\Http\Requests;

use App\Enums\AccountType;
use Illuminate\Foundation\Http\FormRequest;

class AccountRequest extends FormRequest
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
        $isEdit = $this->isMethod('patch');

        $required = 'required_if:type,' . AccountType::PRO->value;

        return [
            'type' => 'required|numeric|in:' . AccountType::values()->implode(','),
            'name' => 'required|string|max:255',
            'external_client_id' => ($isEdit ? 'nullable' : $required) . '|string|max:255',
            'external_client_secret' => ($isEdit ? 'nullable' : $required) . '|string|max:255',
        ];
    }
}
