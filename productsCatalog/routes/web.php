<?php

use App\Livewire\ProductList;
use Illuminate\Support\Facades\Route;
Route::middleware('api')
    ->group(base_path('routes/api.php'));

Route::get('/', ProductList::class)->name('products.view');

