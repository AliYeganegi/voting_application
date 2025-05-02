<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\OperatorStartRequest;
use App\Models\OperatorEndRequest;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('*', function($view){
            $startReq = OperatorStartRequest::pending()->latest()->first();
            $view->with('startConfirmations', $startReq ? $startReq->confirmations()->count() : 0);

            // note: only if there’s an active session
            $endReq = OperatorEndRequest::pending()
                      ->where('session_id', \App\Models\VotingSession::where('is_active',true)->latest()->first()->id ?? 0)
                      ->latest()
                      ->first();
            $view->with('endConfirmations', $endReq ? $endReq->confirmations()->count() : 0);
        });
    }
}
