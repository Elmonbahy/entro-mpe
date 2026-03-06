<?php

namespace App\Exports\Warehouse;

use App\Models\SuratJalan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Carbon\Carbon;

class SuratJalanExport implements FromCollection, WithHeadings
{
  public function collection()
  {
    return SuratJalan::with(['pelanggan', 'kendaraan'])
      ->get()
      ->map(function ($sj) {
        return [
          'nomor_surat_jalan' => $sj->nomor_surat_jalan,
          'nama_pelanggan' => $sj->pelanggan->nama,
          'nama_kendaraan' => $sj->kendaraan->nama,
          'tgl_surat_jalan' => $sj->tgl_surat_jalan ? Carbon::parse($sj->tgl_surat_jalan)->format('d/m/Y') : '-',
          'tgl_kembali_surat_jalan' => $sj->tgl_kembali_surat_jalan ? Carbon::parse($sj->tgl_kembali_surat_jalan)->format('d/m/Y') : '-',
          'staf_logistik' => $sj->staf_logistik,
          'koli' => $sj->koli,
        ];
      });
  }

  public function headings(): array
  {
    return [
      'Nomor Surat Jalan',
      'Nama Pelanggan',
      'Nama Kendaraan',
      'Tanggal Surat Jalan',
      'Terima Kembali',
      'Penanggung Jawab',
      'Jumlah Koli'
    ];
  }
}
