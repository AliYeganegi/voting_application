<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\VoteController;
use App\Http\Controllers\VoterImportController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\OperatorMiddleware;
use App\Http\Middleware\CheckVotingSession;

Auth::routes();

Route::middleware([CheckVotingSession::class])->group(function () {
    Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
});


Route::middleware(['auth', OperatorMiddleware::class, CheckVotingSession::class])->group(function () {
    Route::get('/vote', [VoteController::class, 'index'])->name('vote.index');
    Route::post('/vote/confirm', [VoteController::class, 'confirm'])->name('vote.confirm');
    Route::post('/vote/submit', [VoteController::class, 'submit'])->name('vote.submit');
});

Route::get('/vote/closed', fn() => view('vote.closed'))->name('vote.closed');

Route::middleware(['auth', AdminMiddleware::class])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::post('/start-voting', [AdminController::class, 'startVoting'])->name('admin.startVoting');
    Route::post('/end-voting', [AdminController::class, 'endVoting'])->name('admin.endVoting');
    Route::get('/export', [AdminController::class, 'exportResults'])->name('admin.export');
    Route::get('/sessions', [AdminController::class, 'previousSessions'])->name('admin.sessions');
    Route::get('/sessions/{session}/results', [AdminController::class, 'results'])->name('admin.sessions.results');
    Route::get('/sessions/{session}/results/pdf', [AdminController::class, 'downloadResultPdf'])->name('admin.sessions.results.pdf');

});

Route::middleware(['auth', AdminMiddleware::class])
    ->prefix('admin')
    ->group(function () {
        Route::get('/', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::post('/start', [AdminController::class, 'startVoting'])->name('admin.start');
        Route::post('/stop', [AdminController::class, 'stopVoting'])->name('admin.stop');
        // Route::get('/results', [AdminController::class, 'results'])->name('admin.results');
        // Route::get('/results/pdf', [AdminController::class, 'downloadPdf'])->name('admin.results.pdf');

        // Excel imports
        Route::post('/import-voters', [VoterImportController::class, 'importVoters'])
            ->name('admin.importVoters');
        Route::post('/import-candidates', [VoterImportController::class, 'importCandidates'])
            ->name('admin.importCandidates');
    });
