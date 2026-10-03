<?php

namespace App\Http\Controllers;

use App\Enums\KasJenis;
use App\Models\Alat;
use App\Models\LaporanHarian;
use App\Models\Proyek;
use App\Models\Service;
use App\Models\TransaksiKas;
use App\Support\Format;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View|RedirectResponse
    {
        if (! request()->user()->isBos()) {
            return redirect()->route('laporan.index');
        }

        $today = Format::hariIni();

        $alat = Alat::with(['proyek', 'operator', 'laporanTerakhir.proyek', 'laporanTerakhir.operator'])
            ->orderBy('nama')
            ->get()
            ->map(fn (Alat $a) => [
                'id' => $a->id,
                'kode' => $a->kode,
                'nama' => $a->nama,
                'status' => $a->status->value,
                'posisi_nama' => $a->laporanTerakhir?->proyek?->nama ?? $a->proyek?->nama,
                'posisi_operator' => $a->laporanTerakhir?->operator?->name ?? $a->operator?->name,
                'posisi_updated' => ($a->laporanTerakhir?->updated_at ?? $a->updated_at)?->toIso8601String(),
            ])->all();

        $proyek = Proyek::withCount('alat')->orderBy('nama')->get()->map(fn (Proyek $p) => [
            'id' => $p->id,
            'nama' => $p->nama,
            'lokasi' => $p->lokasi,
            'alat_count' => $p->alat_count,
        ])->all();

        $recentLaporan = LaporanHarian::with(['alat', 'proyek', 'operator'])
            ->latest('tanggal')->latest('id')->limit(5)->get()
            ->map(fn (LaporanHarian $l) => [
                'id' => $l->id,
                'alat_kode' => $l->alat?->kode,
                'proyek_nama' => $l->proyek?->nama,
                'operator_nama' => $l->operator?->name,
                'tanggal' => $l->tanggal?->toDateString(),
                'hm_awal' => (float) $l->hm_awal,
                'hm_akhir' => (float) $l->hm_akhir,
                'solar_jerigen' => $l->solar_jerigen !== null ? (float) $l->solar_jerigen : null,
                'solar_liter' => (float) $l->solar_liter,
            ])->all();

        $recentKas = TransaksiKas::with('proyek')->latest('tanggal')->latest('id')->limit(5)->get()
            ->map(fn (TransaksiKas $k) => [
                'id' => $k->id,
                'jenis' => $k->jenis->value,
                'nominal' => (float) $k->nominal,
                'keterangan' => $k->keterangan,
                'proyek_nama' => $k->proyek?->nama,
                'tanggal' => $k->tanggal?->toDateString(),
            ])->all();

        $recentService = Service::with('alat')->latest('tanggal')->latest('id')->limit(5)->get()
            ->map(fn (Service $s) => [
                'id' => $s->id,
                'alat_kode' => $s->alat?->kode,
                'alat_nama' => $s->alat?->nama,
                'jenis' => $s->jenis,
                'tanggal' => $s->tanggal?->toDateString(),
                'biaya' => $s->biaya !== null ? (float) $s->biaya : null,
            ])->all();

        $data = [
            'today' => $today,
            'alat_aktif' => Alat::where('active', true)->count(),
            'laporan_hari_ini' => LaporanHarian::whereDate('tanggal', $today)->count(),
            'kas' => [
                'masuk' => (float) TransaksiKas::where('jenis', KasJenis::MASUK->value)->sum('nominal'),
                'keluar' => (float) TransaksiKas::where('jenis', KasJenis::KELUAR->value)->sum('nominal'),
                'saldo' => (float) TransaksiKas::where('jenis', KasJenis::MASUK->value)->sum('nominal')
                    - (float) TransaksiKas::where('jenis', KasJenis::KELUAR->value)->sum('nominal'),
            ],
            'alat' => $alat,
            'proyek' => $proyek,
            'recent_laporan' => $recentLaporan,
            'recent_kas' => $recentKas,
            'recent_service' => $recentService,
        ];

        return view('dashboard', ['data' => $data]);
    }
}
