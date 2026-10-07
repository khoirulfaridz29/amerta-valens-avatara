<?php

namespace App\Http\Controllers;

use App\Http\Requests\AlatRequest;
use App\Models\Alat;
use App\Models\Proyek;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AlatController extends Controller
{
    public function index(): View
    {
        $items = Alat::with(['proyek', 'operator'])->orderBy('kode')->get()->map(fn (Alat $a) => [
            'id' => $a->id,
            'nama' => $a->nama,
            'jenis' => $a->jenis,
            'kode' => $a->kode,
            'status' => $a->status->value,
            'active' => $a->active,
            'proyek_id' => $a->proyek_id,
            'operator_id' => $a->operator_id,
            'proyek_nama' => $a->proyek?->nama,
            'operator_nama' => $a->operator?->name,
            'updated_at' => $a->updated_at?->toIso8601String(),
        ])->all();

        return view('alat', [
            'items' => $items,
            'proyek' => Proyek::orderBy('nama')->get(),
            'operators' => User::operator()->orderBy('name')->get(),
        ]);
    }

    public function store(AlatRequest $request): RedirectResponse
    {
        Alat::create($request->validated());

        return redirect()->route('alat.index')->with('status', 'Alat baru ditambahkan');
    }

    public function update(AlatRequest $request, Alat $alat): RedirectResponse
    {
        $alat->update($request->validated());

        return redirect()->route('alat.index')->with('status', 'Data alat diperbarui');
    }

    public function toggleActive(Request $request, Alat $alat): RedirectResponse
    {
        $alat->active = $request->boolean('active');
        $alat->save();

        return back()->with('status', $alat->active ? "{$alat->kode} diaktifkan kembali" : "{$alat->kode} dinonaktifkan");
    }

    public function destroy(Alat $alat): RedirectResponse
    {
        if ($alat->laporan()->exists()) {
            return back()->with('error', 'Alat sudah punya laporan. Nonaktifkan saja daripada dihapus.');
        }

        $alat->delete();

        return redirect()->route('alat.index')->with('status', 'Alat dihapus');
    }
}
