<?php

namespace App\Exports\Gudang;

use App\Models\Pelanggan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PelangganExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Pelanggan::select('id', 'kode', 'nama', 'kota', 'alamat', 'npwp', 'contact_phone', 'contact_person', 'rayon', 'tipe', 'area')->get();
    }

    public function headings(): array
    {
        return [
            'Id',
            'Kode',
            'Nama Pelanggan',
            'Kota',
            'Alamat',
            'NPWP',
            'Contact Phone',
            'Contact Person',
            'Rayon',
            'Tipe',
            'Area',
        ];
    }
}
