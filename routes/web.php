<?php

use App\Livewire\MetalManager;
use App\Livewire\Dashboard;
use App\Livewire\AccountView;
use App\Livewire\Deposits;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', Dashboard::class)->name('dashboard');

Route::get('/accounts/{id}', AccountView::class)->name('accounts.view');

Route::get('/deposits', Deposits::class)->name('deposits');

Route::get('/metals', MetalManager::class)->name('metals');
