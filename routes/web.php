<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UploadController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// 🔽 追加
    Route::resource('uploads', UploadController::class);
});

require __DIR__.'/auth.php';


Route::post('/upload', [UploadController::class, 'store'])->name('upload');
Route::get('/uploads', [UploadController::class, 'index'])->name('uploads.index');

Route::get('/extractions/create/{upload_id}', [ExtractionController::class, 'create'])->name('extractions.create');
