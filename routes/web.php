<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Http\Controllers\AbsenController;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

Route::post('/store-location', function (Request $request) {
    $request->validate([
        'latitude' => 'required|numeric',
        'longitude' => 'required|numeric',
        'accuracy' => 'nullable|numeric'
    ]);

    session([
        'user_location' => [
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'accuracy' => $request->accuracy,
            'timestamp' => now()
        ]
    ]);

    return response()->json([
        'status' => 'success',
        'message' => 'Location stored successfully'
    ]);
})->name('store.location');

Route::post('/absen/masuk', [AbsenController::class, 'masuk'])->name('absen.masuk');
Route::post('/absen/keluar', [AbsenController::class, 'keluar'])->name('absen.keluar');

require __DIR__.'/auth.php';
