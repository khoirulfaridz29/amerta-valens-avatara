<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isBos() ?? false;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'customer' => ['required', 'string', 'max:150'],
            'proyek_id' => ['nullable', 'exists:proyek,id'],
            'tanggal' => ['required', 'date'],
            'jatuh_tempo' => ['nullable', 'date', 'after_or_equal:tanggal'],
            'total' => ['required', 'numeric', 'gt:0'],
            'keterangan' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
