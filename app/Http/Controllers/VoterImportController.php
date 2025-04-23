<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\ValidVoterImport;
use Maatwebsite\Excel\Facades\Excel;

class VoterImportController extends Controller
{
public function import(Request $request)
{
    $request->validate(['file' => 'required|file|mimes:xlsx']);

    Excel::import(new ValidVoterImport, $request->file('file'));

    return redirect()->back()->with('success', 'Voters imported!');
}

}
