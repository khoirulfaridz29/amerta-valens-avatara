<?php

namespace App\Http\Controllers;

use App\Http\Requests\ServiceRequest;
use App\Models\Alat;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function index(): View
    {
        $items = Service::with('alat')->latest('tanggal')->latest('id')->get()->map(fn (Service $s) => [
            'id' => $s->id,
            'alat_id' => $s->alat_id,
            'alat_kode' => $s->alat?->kode,
            'alat_nama' => $s->alat?->nama,
            'tanggal' => $s->tanggal?->toDateString(),
            'jenis' => $s->jenis,
            'keterangan' => $s->keterangan,
            'biaya' => $s->biaya !== null ? (float) $s->biaya : null,
        ])->all();

        return view('service', [
            'items' => $items,
            'alat' => Alat::orderBy('kode')->get(),
        ]);
    }

    public function store(ServiceRequest $request): RedirectResponse
    {
        Service::create($request->validated());

        return redirect()->route('service.index')->with('status', 'Riwayat service ditambahkan');
    }

    public function update(ServiceRequest $request, Service $service): RedirectResponse
    {
        $service->update($request->validated());

        return redirect()->route('service.index')->with('status', 'Riwayat service diperbarui');
    }

    public function destroy(Service $service): RedirectResponse
    {
        $service->delete();

        return redirect()->route('service.index')->with('status', 'Riwayat service dihapus');
    }
}
