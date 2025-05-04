<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\VoteController;
use App\Http\Controllers\VerifierController;
use App\Http\Controllers\OperatorController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\VoterImportController;
use App\Http\Controllers\VotingSessionController;
use App\Http\Middleware\OperatorMiddleware;
use App\Http\Middleware\VerifierMiddleware;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\CheckVotingSession;
use App\Http\Middleware\CheckVerificationQueue;

Auth::routes();

/*
|--------------------------------------------------------------------------
| Public & Home
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/home', [HomeController::class, 'index']);
Route::get('/vote/closed', fn() => view('vote.closed'))->name('vote.closed');

/*
|--------------------------------------------------------------------------
| Operator Panel & Voting
|--------------------------------------------------------------------------
| - /operator/session     → shows how many of 3 have clicked Start/End
| - /operator/session/{s}/approve-start
| - /operator/session/{s}/approve-end
| - then once the session is active: /operator/vote → voting UI
|--------------------------------------------------------------------------
*/

Route::post('/notifications/read', function () {
    auth()->user()->unreadNotifications->markAsRead();
    return back();
})->name('notifications.read');


Route::middleware(['auth', OperatorMiddleware::class])->prefix('operator')->group(function () {
    // (1) operator dashboard: who has confirmed start/end
    Route::get('session', [OperatorController::class, 'panel'])
        ->name('operator.session');

    // (2) operator clicks to approve start or end
    Route::post('session/{session}/approve-start', [OperatorController::class, 'approveStart'])
        ->name('operator.session.approve-start');
    Route::post('session/{session}/approve-end', [OperatorController::class, 'approveEnd'])
        ->name('operator.session.approve-end');

    // (3) once enough operators have approved, session goes live
    Route::get('vote', [VoteController::class, 'index'])
        ->name('vote.index')
        ->middleware(CheckVotingSession::class);

    Route::post('session/create‐and‐approve‐start', [OperatorController::class, 'createAndApproveStart'])
        ->name('operator.session.create-and-approve-start');

    Route::post('{session}/cancel', [OperatorController::class, 'cancelSession'])
        ->name('operator.session.cancel');

    Route::match(['get', 'post'], 'vote/confirm', [VoteController::class, 'confirm'])
        ->name('vote.confirm')
        ->middleware(CheckVerificationQueue::class);

    Route::post('vote/submit', [VoteController::class, 'submit'])
        ->name('vote.submit');

    Route::get('/operator/history', [OperatorController::class, 'history'])->name('operator.history');
});

/*
|--------------------------------------------------------------------------
| Verifier Queue
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', VerifierMiddleware::class])
    ->prefix('verify')
    ->group(function () {
        Route::get('/',   [VerifierController::class, 'index'])->name('verify.index');
        Route::post('/',  [VerifierController::class, 'verify'])->name('verify.verify');
        Route::delete('/verify/queue/{id}', [VerifierController::class, 'removeFromQueue'])->name('verify.queue.remove');
    });

/*
|--------------------------------------------------------------------------
| Admin Area
|--------------------------------------------------------------------------
| - Admin can start/stop alone
| - Session listing, results, ballots, imports, user‑mgmt
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', AdminMiddleware::class])
    ->prefix('admin')
    ->group(function () {
        // Dashboard
        Route::get('dashboard', [AdminController::class, 'dashboard'])
            ->name('admin.dashboard');

        Route::get('/', [AdminController::class, 'dashboard'])->name('admin.dashboard');


        // Direct start / stop
        Route::post('start', [AdminController::class, 'startVoting'])->name('admin.start');
        Route::post('stop',  [AdminController::class, 'endVoting'])->name('admin.stop');

        // Export Excel
        Route::get('export', [AdminController::class, 'exportResults'])
            ->name('admin.export');

        // Sessions index & destroy
        Route::get('sessions',                [AdminController::class, 'previousSessions'])
            ->name('admin.sessions');
        Route::delete('sessions/{session}',  [VotingSessionController::class, 'destroy'])
            ->name('admin.sessions.destroy');

        // Per‑session results, PDF & ballots
        Route::get('sessions/{session}/results',     [AdminController::class, 'results'])
            ->name('admin.sessions.results');
        Route::get('sessions/{session}/results/pdf', [AdminController::class, 'downloadResultPdf'])
            ->name('admin.sessions.results.pdf');
        Route::get('sessions/{session}/ballots',     [AdminController::class, 'viewBallots'])
            ->name('admin.sessions.ballots');

        // Excel imports
        Route::post('import-voters',     [VoterImportController::class, 'importVoters'])
            ->name('admin.importVoters');
        Route::post('import-candidates', [VoterImportController::class, 'importCandidates'])
            ->name('admin.importCandidates');

        // User management
        Route::resource('users', UserManagementController::class)
            ->except(['show']);

        Route::post('/upload-candidate-images', [AdminController::class, 'uploadCandidateImages'])
            ->name('admin.uploadCandidateImages');

        Route::get('/sessions/{session}/ballots/pdf', [AdminController::class, 'downloadBallotsPdf'])
            ->name('admin.sessions.ballots.pdf');
    });

/*
|--------------------------------------------------------------------------
| Notifications
|--------------------------------------------------------------------------
*/
Route::post('notifications/read-all', function () {
    auth()->user()->unreadNotifications->markAsRead();
    return back();
})->middleware('auth')->name('notifications.read-all');
