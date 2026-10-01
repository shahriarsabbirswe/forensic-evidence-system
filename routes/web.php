<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InvestigationCaseController;
use App\Http\Controllers\EvidenceController;

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
});

Route::middleware('auth')->group(function () {

    Route::get('/cases', [InvestigationCaseController::class, 'index'])
        ->middleware('can:case.view')
        ->name('cases.index');

    Route::get('/cases/create', [InvestigationCaseController::class, 'create'])
        ->middleware('can:case.create')
        ->name('cases.create');

    Route::post('/cases', [InvestigationCaseController::class, 'store'])
        ->middleware('can:case.create')
        ->name('cases.store');

    Route::get('/cases/{case}', [InvestigationCaseController::class, 'show'])
        ->middleware('can:case.view')
        ->name('cases.show');

    Route::get('/cases/{case}/edit', [InvestigationCaseController::class, 'edit'])
        ->middleware('can:case.edit')
        ->name('cases.edit');

    Route::put('/cases/{case}', [InvestigationCaseController::class, 'update'])
        ->middleware('can:case.edit')
        ->name('cases.update');

    // Team assignment
    Route::post('/cases/{case}/assign', [InvestigationCaseController::class, 'assign'])
        ->middleware('can:case.assign')
        ->name('cases.assign');

    Route::delete('/cases/{case}/assign/{user}', [InvestigationCaseController::class, 'unassign'])
        ->middleware('can:case.assign')
        ->name('cases.unassign');

    // Evidence is always registered against a case.
    Route::get('/cases/{case}/evidence/create', [EvidenceController::class, 'create'])
        ->middleware('can:evidence.register')
        ->name('evidence.create');

    Route::post('/cases/{case}/evidence', [EvidenceController::class, 'store'])
        ->middleware('can:evidence.register')
        ->name('evidence.store');

    // Once registered, an item is addressed on its own.
    Route::get('/evidence/{evidence}', [EvidenceController::class, 'show'])
        ->middleware('can:evidence.view')
        ->name('evidence.show');

    Route::get('/evidence/{evidence}/edit', [EvidenceController::class, 'edit'])
        ->middleware('can:evidence.edit')
        ->name('evidence.edit');

    Route::put('/evidence/{evidence}', [EvidenceController::class, 'update'])
        ->middleware('can:evidence.edit')
        ->name('evidence.update');

    Route::get('/evidence/{evidence}/download', [EvidenceController::class, 'download'])
        ->middleware('can:evidence.view')
        ->name('evidence.download');

});


require __DIR__.'/auth.php';
