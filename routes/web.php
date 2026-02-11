<?php

use App\Livewire\MetalManager;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/metals', MetalManager::class)->name('metals');
