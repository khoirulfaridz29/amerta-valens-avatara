<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LaporanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'alat_id' => ['required', 'exists:alat,id'],
            'proyek_id' => ['required', 'exists:proyek,id'],
            'tanggal' => ['required', 'date'],
            'hm_awal' => ['required', 'numeric', 'min:0'],
            'hm_akhir' => ['required', 'numeric', 'min:0'],
            'solar_jerigen' => ['nullable', 'numeric', 'min:0'],
            'keterangan' => ['nullable', 'string', 'max:1000'],
            'foto_hm_awal' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'foto_hm_akhir' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'foto_lokasi' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ];
    }
}
