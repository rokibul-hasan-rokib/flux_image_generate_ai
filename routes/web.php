<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImageGenController;
use App\Http\Controllers\ImageGenerationController;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/image-generator', [ImageGenController::class, 'showForm']);
Route::post('/image-generator', [ImageGenController::class, 'generate'])->name('generate.image');


Route::get('/generate-image', [ImageGenerationController::class, 'showForm'])->name('image.form');
Route::post('/generate-image', [ImageGenerationController::class, 'generateImage'])->name('image.generate');