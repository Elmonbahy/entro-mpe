<?php

namespace App\Exports\Supervisor;

use App\Models\Group;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class GroupExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Group::select('id', 'nama')->get();
    }

    public function headings(): array
    {
        return [
            'Id',
            'Nama Group',
        ];
    }
}
