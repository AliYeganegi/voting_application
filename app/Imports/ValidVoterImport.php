<?php

namespace App\Imports;

use App\Models\ValidVoter;
use Maatwebsite\Excel\Concerns\ToModel;

class ValidVoterImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new ValidVoter([
            'voter_id' => $row[0], // assuming ID is in first column
        ]);
    }
}
