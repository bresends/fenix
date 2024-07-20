<?php

use App\Http\Controllers\ExamAppealPdfController;
use App\Http\Controllers\MakeUpExamPdfController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/app');
});


Route::get('pdf/{record}/exam-appeal', ExamAppealPdfController::class)->name('exam-appeal-pdf');

Route::get('pdf/{record}/make-up-exam', MakeUpExamPdfController::class)->name('make-up-exam-pdf');


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
