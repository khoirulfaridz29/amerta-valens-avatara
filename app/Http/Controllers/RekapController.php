<?php

namespace App\Http\Controllers;

use App\Models\LaporanHarian;
use App\Support\Format;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RekapController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if ($user->isBos()) {
            return redirect()->route('laporan.index');
        }

        $bulan = $request->query('bulan');

        $query = LaporanHarian::with(['alat', 'proyek'])
            ->where('user_id', $user->id)
            ->latest('tanggal')
            ->latest('id');

        if ($bulan) {
            $query->where('tanggal', 'like', $bulan.'%');
        }

        $laporan = $query->get();

        $items = $laporan->map(fn (LaporanHarian $l) => [
            'id' => $l->id,
            'tanggal' => $l->tanggal?->toDateString(),
            'alat_kode' => $l->alat?->kode,
            'alat_nama' => $l->alat?->nama,
            'proyek_nama' => $l->proyek?->nama,
            'hm_awal' => $l->hm_awal !== null ? (float) $l->hm_awal : null,
            'hm_akhir' => $l->hm_akhir !== null ? (float) $l->hm_akhir : null,
            'hm_kerja' => (float) $l->hm_akhir - (float) $l->hm_awal,
            'solar_jerigen' => $l->solar_jerigen !== null ? (float) $l->solar_jerigen : null,
            'solar_liter' => (float) $l->solar_liter,
            'keterangan' => $l->keterangan,
            'foto_lokasi' => $l->foto_lokasi ? asset($l->foto_lokasi) : null,
        ])->all();

        $ringkasan = [
            'laporan' => $laporan->count(),
            'hm' => (float) $laporan->sum(fn (LaporanHarian $l) => (float) $l->hm_akhir - (float) $l->hm_awal),
            'solar_jerigen' => (float) $laporan->sum(fn (LaporanHarian $l) => (float) $l->solar_jerigen),
            'solar_liter' => (float) $laporan->sum('solar_liter'),
        ];

        return view('rekap', [
            'items' => $items,
            'ringkasan' => $ringkasan,
            'bulan' => $bulan,
            'bulanIni' => Format::hariIni(),
        ]);
    }
}
