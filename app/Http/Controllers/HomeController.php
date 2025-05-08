<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\VotingSession;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        if (auth()->user()->is_admin) {
            return redirect()->route('admin.dashboard');
        }

        if (auth()->user()->is_verifier) {
            return redirect()->route('verify.index');
        }

        elseif (auth()->user()->is_operator) {
            return redirect()->route('operator.session');
        }

        $session =  $session = VotingSession::where('is_active', true)
            ->latest()
            ->first();
        $candidates = User::where('is_candidate', true)->get();

        return view('vote.index', compact('candidates', 'session'));
    }
}
