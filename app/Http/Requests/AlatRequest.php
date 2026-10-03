<?php

namespace App\Http\Requests;

use App\Enums\AlatStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AlatRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isBos() ?? false;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:150'],
            'jenis' => ['nullable', 'string', 'max:100'],
            'kode' => ['nullable', 'string', 'max:50', Rule::unique('alat', 'kode')->ignore($this->route('alat'))],
            'status' => ['required', Rule::enum(AlatStatus::class)],
            'proyek_id' => ['nullable', 'exists:proyek,id'],
            'operator_id' => ['nullable', 'exists:users,id'],
        ];
    }
}
