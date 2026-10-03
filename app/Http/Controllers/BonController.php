<?php

namespace App\Http\Controllers;

use App\Enums\KasJenis;
use App\Http\Requests\BonRequest;
use App\Models\Bon;
use App\Models\Proyek;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BonController extends Controller
{
    public function index(): View
    {
        $items = Bon::with(['proyek', 'pembayaran'])->orderByDesc('tanggal')->orderByDesc('id')->get()
            ->map(fn (Bon $b) => [
                'id' => $b->id,
                'nomor' => $b->nomor,
                'customer' => $b->customer,
                'proyek_id' => $b->proyek_id,
                'proyek_nama' => $b->proyek?->nama,
                'tanggal' => $b->tanggal?->toDateString(),
                'jatuh_tempo' => $b->jatuh_tempo?->toDateString(),
                'total' => (float) $b->total,
                'keterangan' => $b->keterangan,
                'dibayar' => $b->dibayar,
                'sisa' => $b->sisa,
                'lunas' => $b->lunas,
                'pembayaran' => $b->pembayaran
                    ->where('jenis', KasJenis::MASUK)
                    ->sortByDesc('tanggal')
                    ->map(fn ($k) => [
                        'id' => $k->id,
                        'tanggal' => $k->tanggal?->toDateString(),
                        'nominal' => (float) $k->nominal,
                        'keterangan' => $k->keterangan,
                    ])->values()->all(),
            ])->all();

        $piutang = collect($items)->sum(fn ($i) => max($i['sisa'], 0));

        return view('bon', [
            'items' => $items,
            'proyek' => Proyek::orderBy('nama')->get(),
            'piutang' => $piutang,
        ]);
    }

    public function store(BonRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['nomor'] = $this->nextNomor();

        Bon::create($data);

        return redirect()->route('bon.index')->with('status', 'Bon baru dibuat');
    }

    public function update(BonRequest $request, Bon $bon): RedirectResponse
    {
        $bon->update($request->validated());

        return redirect()->route('bon.index')->with('status', 'Bon diperbarui');
    }

    public function destroy(Bon $bon): RedirectResponse
    {
        if ($bon->pembayaran()->exists()) {
            return back()->with('error', 'Bon ini sudah ada pembayaran, tidak bisa dihapus.');
        }

        $bon->delete();

        return redirect()->route('bon.index')->with('status', 'Bon dihapus');
    }

    private function nextNomor(): string
    {
        $year = now()->year;
        $seq = Bon::whereYear('tanggal', $year)->count() + 1;

        do {
            $nomor = 'BON-'.$year.'-'.str_pad((string) $seq, 4, '0', STR_PAD_LEFT);
            $seq++;
        } while (Bon::where('nomor', $nomor)->exists());

        return $nomor;
    }
}
