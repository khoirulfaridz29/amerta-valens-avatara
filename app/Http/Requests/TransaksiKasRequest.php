<?php

namespace App\Http\Requests;

use App\Enums\KasJenis;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TransaksiKasRequest extends FormRequest
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
            'tanggal' => ['required', 'date'],
            'jenis' => ['required', Rule::enum(KasJenis::class)],
            'nominal' => ['required', 'numeric', 'gt:0'],
            'keterangan' => ['required', 'string', 'max:255'],
            'proyek_id' => ['nullable', 'exists:proyek,id'],
        ];
    }
}
