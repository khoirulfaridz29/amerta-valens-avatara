<?php

use App\Http\Controllers\AlatController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BonController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\KasController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\OperatorController;
use App\Http\Controllers\PengaturanController;
use App\Http\Controllers\ProyekController;
use App\Http\Controllers\RekapController;
use App\Http\Controllers\ServiceController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (! Auth::check()) {
        return redirect()->route('login');
    }

    return redirect()->route(Auth::user()->isBos() ? 'dashboard' : 'laporan.index');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->middleware('throttle:6,1');

    Route::get('/auth/google', [GoogleController::class, 'redirect'])->name('auth.google');
    Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('auth.google.callback');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/alat', [AlatController::class, 'index'])->name('alat.index');

    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::post('/laporan', [LaporanController::class, 'store'])->name('laporan.store');
    Route::put('/laporan/{laporan}', [LaporanController::class, 'update'])->name('laporan.update');
    Route::delete('/laporan/{laporan}', [LaporanController::class, 'destroy'])->name('laporan.destroy');

    Route::get('/rekap', [RekapController::class, 'index'])->name('rekap.index');

    Route::middleware('role:bos')->group(function () {
        Route::post('/alat', [AlatController::class, 'store'])->name('alat.store');
        Route::put('/alat/{alat}', [AlatController::class, 'update'])->name('alat.update');
        Route::put('/alat/{alat}/active', [AlatController::class, 'toggleActive'])->name('alat.active');
        Route::delete('/alat/{alat}', [AlatController::class, 'destroy'])->name('alat.destroy');

        Route::get('/kontrak', [ProyekController::class, 'index'])->name('proyek.index');
        Route::post('/kontrak', [ProyekController::class, 'store'])->name('proyek.store');
        Route::put('/kontrak/{proyek}', [ProyekController::class, 'update'])->name('proyek.update');
        Route::delete('/kontrak/{proyek}', [ProyekController::class, 'destroy'])->name('proyek.destroy');

        Route::get('/kas', [KasController::class, 'index'])->name('kas.index');
        Route::post('/kas', [KasController::class, 'store'])->name('kas.store');
        Route::put('/kas/{kas}', [KasController::class, 'update'])->name('kas.update');
        Route::delete('/kas/{kas}', [KasController::class, 'destroy'])->name('kas.destroy');

        Route::get('/bon', [BonController::class, 'index'])->name('bon.index');
        Route::post('/bon', [BonController::class, 'store'])->name('bon.store');
        Route::put('/bon/{bon}', [BonController::class, 'update'])->name('bon.update');
        Route::delete('/bon/{bon}', [BonController::class, 'destroy'])->name('bon.destroy');

        Route::get('/service', [ServiceController::class, 'index'])->name('service.index');
        Route::post('/service', [ServiceController::class, 'store'])->name('service.store');
        Route::put('/service/{service}', [ServiceController::class, 'update'])->name('service.update');
        Route::delete('/service/{service}', [ServiceController::class, 'destroy'])->name('service.destroy');

        Route::get('/operator', [OperatorController::class, 'index'])->name('operator.index');
        Route::post('/operator', [OperatorController::class, 'store'])->name('operator.store');
        Route::put('/operator/{user}', [OperatorController::class, 'update'])->name('operator.update');
        Route::delete('/operator/{user}', [OperatorController::class, 'destroy'])->name('operator.destroy');

        Route::get('/pengaturan', [PengaturanController::class, 'index'])->name('pengaturan.index');
        Route::put('/pengaturan', [PengaturanController::class, 'update'])->name('pengaturan.update');

        Route::get('/export/kas.csv', [ExportController::class, 'kas'])->name('export.kas');
        Route::get('/export/laporan.csv', [ExportController::class, 'laporan'])->name('export.laporan');
    });
});
