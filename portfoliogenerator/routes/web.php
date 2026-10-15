<?php

use App\Http\Controllers\PortfolioController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PortfolioController::class, 'index'])->name('home');
Route::get('/portfolios/create', [PortfolioController::class, 'create'])->name('portfolios.create');
Route::post('/portfolios', [PortfolioController::class, 'store'])->name('portfolios.store');
Route::get('/manage/{managementToken}/templates', [PortfolioController::class, 'templates'])->name('portfolios.templates');
Route::patch('/manage/{managementToken}/template', [PortfolioController::class, 'selectTemplate'])->name('portfolios.template');
Route::get('/manage/{managementToken}', [PortfolioController::class, 'manage'])->name('portfolios.manage');
Route::get('/manage/{managementToken}/edit', [PortfolioController::class, 'edit'])->name('portfolios.edit');
Route::put('/manage/{managementToken}', [PortfolioController::class, 'update'])->name('portfolios.update');
Route::delete('/manage/{managementToken}', [PortfolioController::class, 'destroy'])->name('portfolios.destroy');
Route::get('/portfolios/{portfolio}', [PortfolioController::class, 'show'])->name('portfolios.show');
