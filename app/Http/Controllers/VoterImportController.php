<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\ValidVoterImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\CandidateImport;
use App\Models\ImportFile;

class VoterImportController extends Controller
{
    public function importVoters(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:xlsx']);

        $file  = $request->file('file');
        $path  = $file->store('imports/voters');

        // Run the actual import
        Excel::import(new ValidVoterImport, $file);

        // Record this upload
        ImportFile::create([
            'type'          => 'voters',
            'original_name' => $file->getClientOriginalName(),
            'path'          => $path,
        ]);

        return back()->with('success', 'Voters imported!');
    }

    public function importCandidates(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:xlsx']);

        $file = $request->file('file');
        $path = $file->store('imports/candidates');

        Excel::import(new CandidateImport, $file);

        ImportFile::create([
            'type'          => 'candidates',
            'original_name' => $file->getClientOriginalName(),
            'path'          => $path,
        ]);

        return back()->with('success', 'Candidates imported!');
    }

}
