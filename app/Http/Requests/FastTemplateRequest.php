<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FastTemplateRequest extends FormRequest
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
            'tag' => 'required|max:255',
            'content' => 'required|min:2'
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'company_id' => $this->session()->get('selectedCompanyId'),
        ]);
    }
}
