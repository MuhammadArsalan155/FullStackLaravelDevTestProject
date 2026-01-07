<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImportController;

Route::middleware('api')->prefix('api')->group(function () {
    Route::post('/import', [ImportController::class, 'import']);
});
