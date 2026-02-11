<?php

use App\Livewire\MetalManager;
use App\Livewire\Dashboard;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', Dashboard::class)->name('dashboard');

Route::get('/metals', MetalManager::class)->name('metals');
