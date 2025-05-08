<?php

namespace App\Http\Controllers;

use App\Models\VotingSession;
use Illuminate\Http\Request;
use App\Models\Vote;
use App\Models\User;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\VoteExport;
use App\Models\Ballot;
use App\Models\ImportFile;
use App\Models\OperatorApproval;
use App\Models\ValidVoter;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Mpdf\Mpdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use ZipArchive;


class AdminController extends Controller
{
    public function dashboard()
    {
        $session           = VotingSession::latest()->first();
        $lastVoterFile     = ImportFile::where('type', 'voters')->latest()->first();
        $lastCandidateFile = ImportFile::where('type', 'candidates')->latest()->first();
        $previousSessions  = VotingSession::where('is_active', false)->orderBy('start_at', 'desc')->get();
        $lastCandidateImagesZip = ImportFile::where('type', 'candidate_images')->latest()->first();
        $operators = User::where('is_operator' , 1)->get();

        // load approvals so far
        $startApps = $session
            ? $session->startApprovals()->with('operator')->get()
            : collect();
        $endApps   = $session
            ? $session->endApprovals()->with('operator')->get()
            : collect();

        return view('admin.dashboard', compact(
            'session',
            'lastVoterFile',
            'lastCandidateFile',
            'previousSessions',
            'startApps',
            'endApps',
            'lastCandidateImagesZip',
            'operators',
        ));
    }

    public function approveStart(VotingSession $session)
    {
        $user = Auth::user();

        // prevent duplicates
        OperatorApproval::firstOrCreate([
            'voting_session_id' => $session->id,
            'operator_id'       => $user->id,
            'action'            => 'start',
        ]);

        // once we have 3 distinct approvals — or if the user is admin —
        $count = $session->startApprovals()->count();
        if ($count >= 3 || $user->is_admin) {
            $session->update(['is_active' => true, 'start_at' => now()]);
        }

        return back();
    }

    public function approveEnd(VotingSession $session)
    {
        $user = Auth::user();

        OperatorApproval::firstOrCreate([
            'voting_session_id' => $session->id,
            'operator_id'       => $user->id,
            'action'            => 'end',
        ]);

        $count = $session->endApprovals()->count();
        if ($count >= 3 || $user->is_admin) {
            $session->update(['is_active' => false, 'end_at' => now()]);
        }

        return back();
    }

    public function startVoting(Request $request)
    {
        // 1) Validate if present; they’re both optional
        $request->validate([
            'name'     => 'required|string|max:255',
            'start_at' => 'nullable|date',
            'end_at'   => 'nullable|date|after:start_at',
        ]);

        // 2) Grab them (will be null if absent)
        $startInput = $request->input('start_at');
        $endInput   = $request->input('end_at');

        // 3) Decide real start time
        $start = $startInput
            ? Carbon::parse($startInput)
            : now();

        // 4) Close any previous session
        VotingSession::where('is_active', true)
            ->update(['is_active' => false]);

        // 5) Reset ballots/voters
        ValidVoter::query()->update(['has_voted' => false]);

        // 6) Build payload
        $payload = [
            'name'      => $request['name'],
            'start_at'  => $start,
            'is_active' => true,
        ];
        if ($endInput) {
            $payload['end_at'] = Carbon::parse($endInput);
        }

        // 7) Create new session
        $session = VotingSession::create($payload);

        OperatorApproval::create([
            'voting_session_id' => $session->id,
            'operator_id'       => Auth::id(),
            'action'              => 'start',
        ]);

        return back()->with('success', 'رأی‌گیری شروع شد');
    }

    public function endVoting()
    {
        // 1) Fetch and close the session
        $session = VotingSession::where('is_active', true)->latest()->first();
        if (! $session) {
            return back()->withErrors(['error' => 'رأی‌گیری فعالی یافت نشد']);
        }
        $session->update([
            'is_active' => false,
            'end_at'   => now(),
        ]);

        OperatorApproval::create([
            'voting_session_id' => $session->id,
            'operator_id'       => Auth::id(),
            'action'              => 'end',
        ]);

        $endApps = $session
            ->endApprovals()
            ->with('operator')
            ->get();

        // 2) Gather results
        $results = User::where('is_candidate', true)
            ->withCount(['votes as votes_count' => function ($q) use ($session) {
                $q->where('voting_session_id', $session->id);
            }])->orderByDesc('votes_count')->get();

        // 3) Instantiate mPDF with RTL enabled
        $configVars = (new \Mpdf\Config\ConfigVariables())->getDefaults();
        $fontDirs   = $configVars['fontDir'];
        $fontVars   = (new \Mpdf\Config\FontVariables())->getDefaults();
        $fontData   = $fontVars['fontdata'];

        $mpdf = new Mpdf([
            'mode'             => 'utf-8',
            'format'           => 'A4-P',
            'margin_left'      => 10,
            'margin_right'     => 10,
            'margin_top'       => 10,
            'margin_bottom'    => 10,
            'fontDir'          => array_merge($fontDirs, [storage_path('fonts')]),
            'fontdata'         => array_merge($fontData, [
                'vazirmatn' => [
                    'R'         => 'Vazirmatn-Regular.ttf',
                    'B'         => 'Vazirmatn-Bold.ttf',
                    'useOTL'    => 0xFF,
                    'useKashida' => 75,
                ]
            ]),
            'default_font'     => 'vazirmatn',
            'autoLangToFont'   => true,
            'autoScriptToLang' => true,
        ]);

        // 4) RTL & meta-language for Farsi
        $mpdf->SetDirectionality('rtl');

        // 5) Render your Blade view (with your fixed RTL styling)
        $html = view('admin.results-pdf-fixed', compact('results', 'session', 'endApps'))->render();
        $mpdf->WriteHTML($html);

        // 6) Output to string and store
        $filePath = 'results/session_' . $session->id . '.pdf';
        Storage::put('public/' . $filePath, $mpdf->Output('', 'S'));
        $session->update(['result_file' => $filePath]);

        return back()->with('success', 'رأی‌گیری پایان یافت و فایل نتایج ایجاد شد.');
    }


    public function exportResults()
    {
        $session = VotingSession::latest()->first();

        if ($session->is_active) {
            return back()->withErrors(['error' => 'رای گیری در حال اجراست گرفتن خروجی پس از اتمام رای گیری ممکن است']);
        }

        return Excel::download(new VoteExport, 'voting_results.xlsx');
    }

    public function results(VotingSession $session)
    {
        if ($session->is_active || ($session->end_at && now()->lt($session->end_at))) {
            return back()->withErrors(['error' => 'نتایج تنها پس از پایان جلسه قابل نمایش است.']);
        }

        $results = User::where('is_candidate', true)
            ->withCount(['votes as votes_count' => function ($q) use ($session) {
                $q->where('voting_session_id', $session->id);
            }])
            ->orderByDesc('votes_count')
            ->get();

        // pass both $results and $session
        return view('admin.results', compact('results', 'session'));
    }

    public function downloadResultPdf(VotingSession $session)
    {
        if (! $session->result_file) {
            abort(404);
        }
        return Storage::download('public/' . $session->result_file);
    }

    public function stopVoting()
    {
        $session = VotingSession::where('is_active', true)->latest()->first();

        if ($session) {
            $session->update([
                'is_active' => false,
                'end_at' => now(),
            ]);
        }

        return redirect()->back()->with('success', 'رای گیری به پایان رسید');
    }

    public function previousSessions()
    {
        $sessions = VotingSession::where('is_active', false)->orderBy('start_at', 'desc')->paginate(10);
        return view('admin.sessions', compact('sessions'));
    }

    public function viewBallots(VotingSession $session)
    {
        // only ended sessions:
        if ($session->is_active || ($session->end_at && now()->lt($session->end_at))) {
            return back()->withErrors(['error' => 'Ballots visible only after session ends.']);
        }

        // load ballots with candidates
        $ballots = Ballot::with('candidates')
            ->where('voting_session_id', $session->id)
            ->paginate(10);

        return view('admin.ballots', compact('session', 'ballots'));
    }

    public function uploadCandidateImages(Request $request)
    {
        $request->validate([
            'images_zip' => 'required|file|mimes:zip',
        ], ['images_zip.mimes' => 'فقط فایل با پسوند ZIP مجاز است.']);

        // 1) Store the ZIP itself on the local disk
        $original = $request->file('images_zip')->getClientOriginalName();
        $zipPath  = $request
            ->file('images_zip')
            ->storeAs('imports/candidate_images', $original, 'local');

        // 2) Make sure extraction dir exists
        Storage::disk('public')->makeDirectory('candidates');

        // 3) Open the ZIP by its absolute path
        $zip = new ZipArchive;
        $full = Storage::disk('local')->path($zipPath);
        if ($zip->open($full) !== true) {
            return back()->withErrors(['error' => 'باز کردن فایل ZIP با خطا مواجه شد.']);
        }

        // 4) Extract only JPGs, overwriting any old ones
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $entry = $zip->getNameIndex($i);
            if (preg_match('/\.(jpe?g)$/i', $entry)) {
                $filename = basename($entry);
                if ($stream = $zip->getStream($entry)) {
                    Storage::disk('public')
                        ->put("candidates/{$filename}", stream_get_contents($stream));
                    fclose($stream);
                }
            }
        }
        $zip->close();

        // 5) Record the import (now path is never null)
        ImportFile::create([
            'type'          => 'candidate_images',
            'original_name' => $original,
            'path'          => $zipPath,
        ]);

        return back()->with('success', 'تصاویر نامزدها با موفقیت بارگذاری و استخراج شدند.');
    }

    public function downloadBallotsPdf(VotingSession $session)
    {
        // only after session has ended
        if ($session->is_active || ! $session->end_at) {
            return back()->withErrors(['error' => 'نتایج تنها پس از پایان جلسه قابل دانلود است.']);
        }

        $ballots = Ballot::with('candidates')->where('voting_session_id', $session->id)->get();

        // Instantiate mPDF with RTL and our Vazirmatn font
        $configVars = (new \Mpdf\Config\ConfigVariables())->getDefaults();
        $fontDirs   = $configVars['fontDir'];
        $fontVars   = (new \Mpdf\Config\FontVariables())->getDefaults();
        $fontData   = $fontVars['fontdata'];

        $mpdf = new Mpdf([
            'mode'             => 'utf-8',
            'format'           => 'A4-P',
            'margin_left'      => 5,
            'margin_right'     => 5,
            'margin_top'       => 5,
            'margin_bottom'    => 5,
            'fontDir'          => array_merge($fontDirs, [storage_path('fonts')]),
            'fontdata'         => array_merge($fontData, [
                'vazirmatn' => [
                    'R'         => 'Vazirmatn-Regular.ttf',
                    'B'         => 'Vazirmatn-Bold.ttf',
                    'useOTL'    => 0xFF,
                    'useKashida' => 75,
                ]
            ]),
            'default_font'     => 'vazirmatn',
            'autoLangToFont'   => true,
            'autoScriptToLang' => true,
        ]);

        $mpdf->SetDirectionality('rtl');

        // Render our new Blade PDF template
        $html = view('admin.ballots-pdf', compact('session', 'ballots'))->render();
        ini_set('pcre.backtrack_limit', 10_000_000);
        $mpdf->WriteHTML($html);

        $filename = "ballots_session_{$session->id}.pdf";
        $output   = $mpdf->Output($filename, 'S');

        return response($output, 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }
}
