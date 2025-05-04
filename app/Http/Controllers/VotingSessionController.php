<?php

namespace App\Http\Controllers;

use App\Exports\VotingResultsExcelExport;
use App\Models\VotingSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class VotingSessionController extends Controller
{
    /**
     * Delete a completed voting session, its PDF and its votes (cascade).
     */
    public function destroy(VotingSession $session)
    {
        // Only allow deletion of sessions that have ended
        if ($session->is_active) {
            return back()->withErrors(['error' => 'نمی‌توانید جلسه در حال اجرا را حذف کنید.']);
        }

        // Delete the stored PDF file if it exists
        if ($session->result_file && Storage::exists('public/' . $session->result_file)) {
            Storage::delete('public/' . $session->result_file);
        }

        // Delete the session (votes will be cascade-deleted)
        $session->delete();

        return back()->with('success', 'جلسه و داده‌های آن با موفقیت حذف شد.');
    }

    public function exportResultsExcel(VotingSession $session)
    {
        if ($session->is_active || ! $session->end_at) {
            return back()->withErrors(['error' => 'نتایج تنها پس از پایان جلسه قابل دانلود است.']);
        }

        $filename = 'نتایج جلسه ' . $session->name . '.xlsx';
        return Excel::download(new VotingResultsExcelExport($session->id), $filename);
    }
}
