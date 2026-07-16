<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Public\DaftarPemilih;
use App\Livewire\Public\ProfilPaslon;
use App\Livewire\Public\Voting;
use App\Livewire\Public\HasilSuara;
use App\Livewire\Public\Beranda;

Route::get('/', Beranda::class)->name('beranda');



Route::get('/pemilihan/{pemilihan}', ProfilPaslon::class)->name('pemilihan.profil');
Route::get('/pemilihan/{pemilihan}/daftar', DaftarPemilih::class)->name('pemilihan.daftar');
Route::get('/pemilihan/{pemilihan}/voting', Voting::class)->name('pemilihan.voting');
Route::get('/pemilihan/{pemilihan}/hasil', HasilSuara::class)->name('pemilihan.hasil');