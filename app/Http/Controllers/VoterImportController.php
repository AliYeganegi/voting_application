<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ValidVoterImport;
use App\Imports\CandidateImport;
use App\Models\ImportFile;
use App\Models\ValidVoter;
use App\Models\Vote;
use App\Models\User;

class VoterImportController extends Controller
{
    public function importVoters(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:xlsx']);

        // Remove previous voters and all votes
        ValidVoter::truncate();
        Vote::truncate();

        $file = $request->file('file');
        $path = $file->store('imports/voters');

        // Run the actual import
        Excel::import(new ValidVoterImport, $file);

        // Record this upload
        ImportFile::create([
            'type'          => 'voters',
            'original_name' => $file->getClientOriginalName(),
            'path'          => $path,
        ]);

        return back()->with('success', 'Voters imported! Previous records cleared.');
    }

    public function importCandidates(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:xlsx']);

        // Remove previous candidates and all votes
        User::where('is_candidate', true)->delete();
        Vote::truncate();

        $file = $request->file('file');
        $path = $file->store('imports/candidates');

        Excel::import(new CandidateImport, $file);

        ImportFile::create([
            'type'          => 'candidates',
            'original_name' => $file->getClientOriginalName(),
            'path'          => $path,
        ]);

        return back()->with('success', 'Candidates imported! Previous records cleared.');
    }
}
