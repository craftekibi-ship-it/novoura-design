<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\StudioController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/pano');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'show'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/pano', [BrandController::class, 'pano'])->name('pano');
    Route::get('/marka/{slug}', [BrandController::class, 'switch'])->name('marka.switch');

    Route::get('/toplu', [StudioController::class, 'batch'])->name('toplu');
    Route::get('/carousel', [StudioController::class, 'carousel'])->name('carousel');
    Route::get('/studio/post/{post}', [StudioController::class, 'openPost'])->name('studio.post');
    Route::get('/studio/{itemId?}', [StudioController::class, 'show'])->name('studio');
    Route::post('/studio/{itemId}/generate', [StudioController::class, 'generate'])->name('studio.generate');

    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::post('/posts/{post}/durum', [PostController::class, 'updateStatus'])->name('posts.durum');
    Route::post('/posts/{post}/planla', [PostController::class, 'plan'])->name('posts.planla');
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');

    Route::get('/kuyruk', [PostController::class, 'queue'])->name('kuyruk');
    Route::get('/takvim', [PostController::class, 'calendar'])->name('takvim');

    Route::get('/plan', [PlanController::class, 'show'])->name('plan');
    Route::get('/plan/pdf', [PlanController::class, 'pdf'])->name('plan.pdf');
    Route::post('/plan/suggest', [PlanController::class, 'suggest'])->name('plan.suggest');
    Route::post('/plan/approve', [PlanController::class, 'approve'])->name('plan.approve');
});
