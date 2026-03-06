<?php

namespace App\Exports\Keuangan;

use App\Models\Pelanggan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PelangganExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Pelanggan::select('id', 'kode', 'nama', 'kota', 'alamat', 'npwp', 'contact_phone', 'contact_person', 'rayon', 'tipe', 'tipe_harga', 'area', 'plafon_hutang', 'limit_hari')->get();
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
            'Tipe Harga',
            'Area',
            'Plafon Hutang',
            'Limit Hari'
        ];
    }
}
