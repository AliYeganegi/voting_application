<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\ValidVoterImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\CandidateImport;

class VoterImportController extends Controller
{
    public function importVoters(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:xlsx']);
        Excel::import(new ValidVoterImport, $request->file('file'));
        return back()->with('success', 'Voters imported!');
    }

    public function importCandidates(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:xlsx']);
        Excel::import(new CandidateImport, $request->file('file'));
        return back()->with('success', 'Candidates imported!');
    }
}
