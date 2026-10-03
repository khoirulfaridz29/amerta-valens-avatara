<?php

namespace Database\Seeders;

use App\Enums\AlatStatus;
use App\Enums\KasJenis;
use App\Enums\Role;
use App\Models\Alat;
use App\Models\Bon;
use App\Models\LaporanHarian;
use App\Models\Proyek;
use App\Models\Service;
use App\Models\TransaksiKas;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@avatara.id'],
            ['name' => 'Admin Avatara', 'phone' => '0812-3456-7890', 'password' => Hash::make('admin123'), 'role' => Role::BOS, 'is_active' => true],
        );

        $andi = User::updateOrCreate(
            ['email' => 'operator@avatara.id'],
            ['name' => 'Andi Pratama', 'phone' => '0813-0000-1111', 'password' => Hash::make('operator123'), 'role' => Role::OPERATOR, 'is_active' => true],
        );

        $sugeng = User::updateOrCreate(
            ['email' => 'sugeng@avatara.id'],
            ['name' => 'Sugeng Riyadi', 'phone' => '0853-2222-3333', 'password' => Hash::make('sugeng123'), 'role' => Role::OPERATOR, 'is_active' => true],
        );

        $tol = Proyek::updateOrCreate(['nama' => 'Tol Balikpapan - Samarinda'], ['lokasi' => 'KM 34, Balikpapan']);
        $tambang = Proyek::updateOrCreate(['nama' => 'Tambang Kutai Kartanegara'], ['lokasi' => 'Kutai Kartanegara']);
        Proyek::updateOrCreate(['nama' => 'Normalisasi Sungai Samarinda'], ['lokasi' => 'Samarinda']);

        $ex1 = Alat::updateOrCreate(
            ['kode' => 'EX-01'],
            ['nama' => 'CAT 320', 'jenis' => 'Excavator', 'status' => AlatStatus::AKTIF, 'proyek_id' => $tol->id, 'operator_id' => $andi->id, 'active' => true],
        );
        $ex2 = Alat::updateOrCreate(
            ['kode' => 'EX-02'],
            ['nama' => 'Komatsu PC200', 'jenis' => 'Excavator', 'status' => AlatStatus::AKTIF, 'proyek_id' => $tambang->id, 'operator_id' => $sugeng->id, 'active' => true],
        );
        $ex3 = Alat::updateOrCreate(
            ['kode' => 'EX-03'],
            ['nama' => 'Hitachi ZX200', 'jenis' => 'Excavator', 'status' => AlatStatus::PERBAIKAN, 'proyek_id' => null, 'operator_id' => null, 'active' => true],
        );

        $kas = [
            [10, KasJenis::MASUK, 45000000, 'Sewa alat minggu ke-2 (PT Karya Bengalon)', $tol],
            [8, KasJenis::MASUK, 30000000, 'Termin pembayaran proyek tambang', $tambang],
            [7, KasJenis::KELUAR, 12500000, 'Solar 4.800 L untuk EX-01 & EX-02', $tol],
            [5, KasJenis::KELUAR, 6200000, 'Honor operator bulan ini', $tambang],
            [3, KasJenis::KELUAR, 3800000, 'Sparepart track EX-03 (roller & sprocket)', $tol],
            [2, KasJenis::KELUAR, 1450000, 'Servis berkala EX-02 (oli & filter)', $tambang],
            [1, KasJenis::MASUK, 15000000, 'Pelunasan sewa bulanan (PT Green Mining)', null],
        ];
        foreach ($kas as [$ago, $jenis, $nominal, $ket, $proyek]) {
            TransaksiKas::firstOrCreate(
                ['keterangan' => $ket, 'nominal' => $nominal],
                ['tanggal' => now()->subDays($ago)->toDateString(), 'jenis' => $jenis, 'proyek_id' => $proyek?->id],
            );
        }

        $laporan = [
            [2, $ex1, $tol, $andi, 1200, 1208.5, 3, 'Penggalian & penimbunan area KM 34, cuaca cerah'],
            [1, $ex1, $tol, $andi, 1208.5, 1216.5, 4, 'Cut and fill lanjutan, checklist pagi selesai'],
            [1, $ex2, $tambang, $sugeng, 980, 987.5, 3.5, 'Loading overburden ke dump truck'],
            [0, $ex1, $tol, $andi, 1216.5, 1224.5, 4, 'Penggalian pondasi girder 3'],
            [0, $ex2, $tambang, $sugeng, 987.5, 994.5, 3, 'Pembersihan area front, hujan siang'],
        ];
        foreach ($laporan as [$ago, $alat, $proyek, $op, $hmAwal, $hmAkhir, $jerigen, $ket]) {
            LaporanHarian::firstOrCreate(
                ['alat_id' => $alat->id, 'tanggal' => now()->subDays($ago)->toDateString(), 'keterangan' => $ket],
                ['proyek_id' => $proyek->id, 'user_id' => $op->id, 'hm_awal' => $hmAwal, 'hm_akhir' => $hmAkhir, 'solar_jerigen' => $jerigen],
            );
        }

        $service = [
            [20, $ex1, 1180, 'Servis berkala', 'Ganti oli mesin & filter', 2500000],
            [14, $ex3, 965, 'Perbaikan', 'Ganti roller & sprocket track', 8500000],
            [6, $ex2, 985, 'Servis berkala', 'Servis 500 HM, cek hidrolik', 3200000],
        ];
        foreach ($service as [$ago, $alat, $hm, $jenis, $ket, $biaya]) {
            Service::firstOrCreate(
                ['alat_id' => $alat->id, 'keterangan' => $ket],
                ['tanggal' => now()->subDays($ago)->toDateString(), 'hm' => $hm, 'jenis' => $jenis, 'biaya' => $biaya],
            );
        }

        $bon = Bon::updateOrCreate(
            ['nomor' => 'BON-'.now()->year.'-0001'],
            [
                'customer' => 'PT Karya Bengalon',
                'proyek_id' => $tol->id,
                'tanggal' => now()->subDays(12)->toDateString(),
                'jatuh_tempo' => now()->addDays(18)->toDateString(),
                'total' => 60000000,
                'keterangan' => 'Sewa EX-01 10 hari',
            ],
        );

        TransaksiKas::updateOrCreate(
            ['keterangan' => 'Cicilan '.$bon->nomor, 'nominal' => 20000000],
            ['tanggal' => now()->subDays(5)->toDateString(), 'jenis' => KasJenis::MASUK, 'bon_id' => $bon->id],
        );
    }
}
