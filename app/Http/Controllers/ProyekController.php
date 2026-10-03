<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProyekRequest;
use App\Models\Proyek;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProyekController extends Controller
{
    public function index(): View
    {
        $items = Proyek::withCount('alat')->orderBy('nama')->get()->map(fn (Proyek $p) => [
            'id' => $p->id,
            'nama' => $p->nama,
            'lokasi' => $p->lokasi,
            'alat_count' => $p->alat_count,
        ])->all();

        return view('proyek', ['items' => $items]);
    }

    public function store(ProyekRequest $request): RedirectResponse
    {
        Proyek::create($request->validated());

        return redirect()->route('proyek.index')->with('status', 'Kontrak / lokasi baru ditambahkan');
    }

    public function update(ProyekRequest $request, Proyek $proyek): RedirectResponse
    {
        $proyek->update($request->validated());

        return redirect()->route('proyek.index')->with('status', 'Kontrak / lokasi diperbarui');
    }
}
