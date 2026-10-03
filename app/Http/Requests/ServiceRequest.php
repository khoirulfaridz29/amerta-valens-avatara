<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ServiceRequest extends FormRequest
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
            'alat_id' => ['required', 'exists:alat,id'],
            'tanggal' => ['required', 'date'],
            'jenis' => ['nullable', 'string', 'max:100'],
            'keterangan' => ['nullable', 'string', 'max:1000'],
            'biaya' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
