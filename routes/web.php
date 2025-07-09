<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImageGenController;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/image-generator', [ImageGenController::class, 'showForm']);
Route::post('/image-generator', [ImageGenController::class, 'generate'])->name('generate.image');
