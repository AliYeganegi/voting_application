<?php

namespace App\Imports;

use App\Models\ValidVoter;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

// 1) Keep your Persian headers exactly as they appear
HeadingRowFormatter::default('none');

class ValidVoterImport implements ToModel, WithHeadingRow
{
    /**
     * Tell Laravel-Excel that row 1 is the header row
     */
    public function headingRow(): int
    {
        return 1;
    }

    /**
     * Map each incoming row (keyed by your exact header names)
     */
    public function model(array $row)
    {
        // 2) Trim all the keys so we drop trailing spaces
        $clean = [];
        foreach ($row as $key => $value) {
            $cleanKey        = trim($key);
            $clean[$cleanKey] = $value;
        }

        // 3) Pull out the national ID using the trimmed key
        $natId = trim($clean['کدملی'] ?? '');

        // 4) If it's empty, skip this row
        if ($natId === '') {
            return null;
        }

        return new ValidVoter([
            'voter_id'       => $natId,
            'first_name'     => trim($clean['نام'] ?? ''),
            'last_name'      => trim($clean['نام خانوادگی'] ?? ''),
            'license_number' => trim($clean['شما ره پروانه'] ?? ''),
        ]);
    }
}
