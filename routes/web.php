<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ConvertController;

Route::get('/', function () {
    return view('welcome');
});



Route::get('/convert', [ConvertController::class, 'index'])->name('convert.index');
Route::post('/convert', [ConvertController::class, 'convert'])->name('convert.process');

Route::get('/convert/nomer', [ConvertController::class, 'nomer'])->name('convert.nomer');
Route::post('/convert/nomer', [ConvertController::class, 'convertnomer'])->name('convert.convertnomer');

Route::get('/convert/nomer/bagan', [ConvertController::class, 'nomerbagan'])->name('convert.nomerbagan');
Route::post('/convert/nomer/bagan', [ConvertController::class, 'convertnomerbagan'])->name('convert.convertnomerbagan');
