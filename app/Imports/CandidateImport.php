<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

// 1) Keep your Persian headings intact
HeadingRowFormatter::default('none');

class CandidateImport implements ToModel, WithHeadingRow
{
    /**
     * Specify that row 1 contains the headings.
     */
    public function headingRow(): int
    {
        return 1;
    }

    /**
     * Map each Excel row into a User model.
     *
     * @param  array<string,mixed>  $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // 2) Grab and clean the national ID
        $nationalId = trim($row['شماره ملی'] ?? '');

        // 3) Skip rows without a valid national ID
        if ($nationalId === '') {
            return null;
        }

        // 4) Create the user as a candidate
        return new User([
            'name'           => trim("{$row['نام']} {$row['نام خانوادگی']}"),
            'email'          => Str::slug($row['نام'].'-'.$row['نام خانوادگی']).'@example.com',
            'password'       => bcrypt('candidate'),
            'national_id'    => $nationalId,
            'license_number' => trim($row['شماره پروانه'] ?? ''),
            'profile_image'  => $nationalId.'.jpg',
            'is_candidate'   => true,
        ]);
    }
}
