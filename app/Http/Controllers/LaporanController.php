<?php

namespace App\Http\Controllers;

use App\Http\Requests\LaporanRequest;
use App\Models\Alat;
use App\Models\LaporanHarian;
use App\Models\Proyek;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\View\View;

class LaporanController extends Controller
{
    public function index(Request $request): View
    {
        $isBos = $request->user()->isBos();
        $query = LaporanHarian::with(['alat', 'proyek', 'operator']);

        if (! $isBos) {
            $query->where('user_id', $request->user()->id);
        } else {
            if ($alatId = $request->query('alat_id')) {
                $query->where('alat_id', $alatId);
            }
            if ($proyekId = $request->query('proyek_id')) {
                $query->where('proyek_id', $proyekId);
            }
            if ($bulan = $request->query('bulan')) {
                $query->where('tanggal', 'like', $bulan.'%');
            }
        }

        $items = $query->latest('tanggal')->latest('id')->get()->map(fn (LaporanHarian $l) => [
            'id' => $l->id,
            'alat_id' => $l->alat_id,
            'proyek_id' => $l->proyek_id,
            'alat_kode' => $l->alat?->kode,
            'alat_nama' => $l->alat?->nama,
            'proyek_nama' => $l->proyek?->nama,
            'operator_nama' => $l->operator?->name,
            'tanggal' => $l->tanggal?->toDateString(),
            'hm_awal' => $l->hm_awal !== null ? (float) $l->hm_awal : null,
            'hm_akhir' => $l->hm_akhir !== null ? (float) $l->hm_akhir : null,
            'solar_jerigen' => $l->solar_jerigen !== null ? (float) $l->solar_jerigen : null,
            'solar_liter' => (float) $l->solar_liter,
            'keterangan' => $l->keterangan,
            'foto_hm_awal' => $l->foto_hm_awal ? '/'.ltrim($l->foto_hm_awal, '/') : null,
            'foto_hm_akhir' => $l->foto_hm_akhir ? '/'.ltrim($l->foto_hm_akhir, '/') : null,
            'foto_lokasi' => $l->foto_lokasi ? '/'.ltrim($l->foto_lokasi, '/') : null,
        ])->all();

        return view('laporan', [
            'items' => $items,
            'alat' => Alat::where('active', true)->orderBy('kode')->get(),
            'proyek' => Proyek::orderBy('nama')->get(),
        ]);
    }

    public function store(LaporanRequest $request): RedirectResponse
    {
        $data = $request->safe()->only([
            'alat_id', 'proyek_id', 'tanggal', 'hm_awal', 'hm_akhir', 'solar_jerigen', 'keterangan',
        ]);
        $data['user_id'] = $request->user()->id;
        $data['solar_jerigen'] = $request->input('solar_jerigen') ?: null;

        foreach (['foto_hm_awal', 'foto_hm_akhir', 'foto_lokasi'] as $field) {
            if ($request->hasFile($field)) {
                $data[$field] = $this->simpanFoto($request->file($field));
            }
        }

        LaporanHarian::create($data);

        $redirect = $request->user()->isBos()
            ? route('laporan.index')
            : route('laporan.index', ['tab' => 'riwayat']);

        return redirect($redirect)->with('status', 'Laporan harian terkirim');
    }

    public function update(LaporanRequest $request, LaporanHarian $laporan): RedirectResponse
    {
        $user = $request->user();
        if (! $user->isBos() && $laporan->user_id !== $user->id) {
            abort(403);
        }

        $data = $request->safe()->only(['alat_id', 'proyek_id', 'tanggal', 'hm_awal', 'hm_akhir', 'solar_jerigen', 'keterangan']);
        $data['solar_jerigen'] = $request->input('solar_jerigen') ?: null;

        foreach (['foto_hm_awal', 'foto_hm_akhir', 'foto_lokasi'] as $field) {
            if ($request->hasFile($field)) {
                if ($laporan->{$field}) {
                    @unlink(public_path($laporan->{$field}));
                }
                $data[$field] = $this->simpanFoto($request->file($field));
            }
        }

        $laporan->update($data);

        return back()->with('status', 'Laporan diperbarui');
    }

    public function destroy(Request $request, LaporanHarian $laporan): RedirectResponse
    {
        $user = $request->user();
        if (! $user->isBos() && $laporan->user_id !== $user->id) {
            abort(403);
        }

        foreach (['foto_hm_awal', 'foto_hm_akhir', 'foto_lokasi'] as $field) {
            if ($laporan->{$field}) {
                @unlink(public_path($laporan->{$field}));
            }
        }

        $laporan->delete();

        return back()->with('status', 'Laporan dihapus');
    }

    private function simpanFoto(UploadedFile $file): string
    {
        $dir = public_path('uploads/laporan');
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $img = @imagecreatefromstring((string) file_get_contents($file->getRealPath()));

        if ($img === false) {
            $ext = strtolower($file->extension() ?: 'jpg');
            if (! in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true)) {
                $ext = 'jpg';
            }
            $name = Str::random(24).'.'.$ext;
            $file->move($dir, $name);

            return 'uploads/laporan/'.$name;
        }

        $width = imagesx($img);
        $height = imagesy($img);
        $max = 1600;

        if ($width > $max || $height > $max) {
            $ratio = min($max / $width, $max / $height);
            $newWidth = max(1, (int) round($width * $ratio));
            $newHeight = max(1, (int) round($height * $ratio));
            $resized = imagecreatetruecolor($newWidth, $newHeight);
            imagecopyresampled($resized, $img, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            imagedestroy($img);
            $img = $resized;
        }

        $name = Str::random(24).'.jpg';
        imagejpeg($img, $dir.DIRECTORY_SEPARATOR.$name, 75);
        imagedestroy($img);

        return 'uploads/laporan/'.$name;
    }
}
