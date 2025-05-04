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

        $clean = [];
        foreach ($row as $key => $value) {
            $cleanKey        = trim($key);
            $clean[$cleanKey] = $value;
        }

        $natId = trim($clean['کدملی'] ?? '');

        if ($natId === '') {
            return null;
        }

        return new User([
            'national_id'       => $natId,
            'name'           => trim("{$row['نام']} {$row['نام خانوادگی']}"),
            'license_number' => trim($clean['شما ره پروانه'] ?? ''),
            'password'       => bcrypt('candidate'),
            'profile_image'  => $natId . '.jpg',
            'is_candidate'   => true,
            'email'          => Str::slug($row['نام'].'-'.$row['نام خانوادگی']).'@example.com',
        ]);
    }
}
