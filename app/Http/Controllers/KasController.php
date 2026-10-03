<?php

namespace App\Http\Controllers;

use App\Enums\KasJenis;
use App\Http\Requests\TransaksiKasRequest;
use App\Models\Proyek;
use App\Models\TransaksiKas;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class KasController extends Controller
{
    public function index(Request $request): View
    {
        $query = TransaksiKas::with('proyek');

        if (in_array($request->query('jenis'), ['masuk', 'keluar'], true)) {
            $query->where('jenis', $request->query('jenis'));
        }
        if ($proyekId = $request->query('proyek_id')) {
            $query->where('proyek_id', $proyekId);
        }
        if ($start = $request->query('start')) {
            $query->whereDate('tanggal', '>=', $start);
        }
        if ($end = $request->query('end')) {
            $query->whereDate('tanggal', '<=', $end);
        }

        $items = $query->latest('tanggal')->latest('id')->get()->map(fn (TransaksiKas $k) => [
            'id' => $k->id,
            'jenis' => $k->jenis->value,
            'nominal' => (float) $k->nominal,
            'keterangan' => $k->keterangan,
            'proyek_nama' => $k->proyek?->nama,
            'tanggal' => $k->tanggal?->toDateString(),
        ])->all();

        $masuk = (float) TransaksiKas::where('jenis', KasJenis::MASUK->value)->sum('nominal');
        $keluar = (float) TransaksiKas::where('jenis', KasJenis::KELUAR->value)->sum('nominal');

        return view('kas', [
            'data' => [
                'summary' => ['masuk' => $masuk, 'keluar' => $keluar, 'saldo' => $masuk - $keluar],
                'items' => $items,
            ],
            'proyek' => Proyek::orderBy('nama')->get(),
            'bulanan' => $this->rekap('%Y-%m'),
            'tahunan' => $this->rekap('%Y'),
        ]);
    }

    public function store(TransaksiKasRequest $request): RedirectResponse
    {
        TransaksiKas::create($request->validated());

        return redirect()->route('kas.index')->with('status', 'Transaksi kas tercatat');
    }

    /**
     * @return array<int, array{periode: string, masuk: float, keluar: float, saldo: float}>
     */
    private function rekap(string $format): array
    {
        return DB::table('transaksi_kas')
            ->selectRaw("DATE_FORMAT(tanggal, '{$format}') as periode")
            ->selectRaw("SUM(CASE WHEN jenis = 'masuk' THEN nominal ELSE 0 END) as masuk")
            ->selectRaw("SUM(CASE WHEN jenis = 'keluar' THEN nominal ELSE 0 END) as keluar")
            ->groupBy('periode')
            ->orderByDesc('periode')
            ->get()
            ->map(fn ($r) => [
                'periode' => $r->periode,
                'masuk' => (float) $r->masuk,
                'keluar' => (float) $r->keluar,
                'saldo' => (float) $r->masuk - (float) $r->keluar,
            ])->all();
    }
}
