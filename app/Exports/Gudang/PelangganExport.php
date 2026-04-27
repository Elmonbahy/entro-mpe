<?php

namespace App\Exports\Gudang;

use App\Models\Pelanggan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PelangganExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Pelanggan::select('id', 'nama', 'kota', 'alamat', 'contact_phone', 'contact_person', 'tipe')->get();
    }

    public function headings(): array
    {
        return [
            'Id',
            'Nama Pelanggan',
            'Kota',
            'Alamat',
            'Contact Phone',
            'Contact Person',
            'Tipe',
        ];
    }
}
