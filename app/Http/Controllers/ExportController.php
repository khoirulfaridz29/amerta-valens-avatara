<?php

namespace App\Http\Controllers;

use App\Models\LaporanHarian;
use App\Models\TransaksiKas;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    public function kas(): StreamedResponse
    {
        $rows = [['Tanggal', 'Jenis', 'Nominal', 'Keterangan', 'Kontrak / Lokasi']];

        TransaksiKas::with('proyek')->orderBy('tanggal')->chunk(500, function ($chunk) use (&$rows) {
            foreach ($chunk as $k) {
                $rows[] = [
                    $k->tanggal?->toDateString(),
                    $k->jenis->value,
                    (float) $k->nominal,
                    $k->keterangan,
                    $k->proyek?->nama ?? 'Umum',
                ];
            }
        });

        return $this->csv('buku-kas.csv', $rows);
    }

    public function laporan(): StreamedResponse
    {
        $rows = [['Tanggal', 'Alat', 'Kontrak / Lokasi', 'Operator', 'HM Awal', 'HM Akhir', 'Solar (Jerigen)', 'Solar (Liter)', 'Keterangan']];

        LaporanHarian::with(['alat', 'proyek', 'operator'])->orderBy('tanggal')->chunk(500, function ($chunk) use (&$rows) {
            foreach ($chunk as $l) {
                $rows[] = [
                    $l->tanggal?->toDateString(),
                    $l->alat?->nama,
                    $l->proyek?->nama,
                    $l->operator?->name,
                    $l->hm_awal !== null ? (float) $l->hm_awal : '',
                    $l->hm_akhir !== null ? (float) $l->hm_akhir : '',
                    $l->solar_jerigen !== null ? (float) $l->solar_jerigen : '',
                    (float) $l->solar_liter,
                    $l->keterangan,
                ];
            }
        });

        return $this->csv('laporan-harian.csv', $rows);
    }

    /**
     * @param  array<int, array<int, mixed>>  $rows
     */
    private function csv(string $filename, array $rows): StreamedResponse
    {
        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            foreach ($rows as $row) {
                fputcsv($out, $row);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}
