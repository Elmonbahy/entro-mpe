<?php

namespace App\Exports\Gudang;

use App\Models\Mutation;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class KartuStockExport implements FromCollection, WithHeadings
{
  protected $tgl_awal;
  protected $tgl_akhir;
  protected $barang_id;

  public function __construct($tgl_awal, $tgl_akhir, $barang_id)
  {
    $this->tgl_awal = $tgl_awal;
    $this->tgl_akhir = $tgl_akhir;
    $this->barang_id = $barang_id;
  }

  public function collection()
  {
    $query = Mutation::query()
      ->with('mutationable')
      ->orderBy('tgl_mutation', 'asc')
      ->orderBy('id', 'asc');

    if ($this->barang_id) {
      $query->where('barang_id', $this->barang_id);
    }

    if ($this->tgl_awal && $this->tgl_akhir) {
      $query->whereBetween('tgl_mutation', [
        $this->tgl_awal,
        Carbon::parse($this->tgl_akhir)->endOfDay(),
      ]);
    }

    return $query->get()->map(function ($item) {
      return [
        'nomor_faktur' => $item->nomor_faktur ?? '',
        'Tanggal' => Carbon::parse($item->tgl_mutation)->format('d/m/Y'),
        'Supplier/Pelanggan' => $item->user,
        'Batch' => $item->batch ?? '-',
        'Expired' => $item->Expired ?? '-',
        'Mutasi' => $item->mutasi,
        'Posisi' => (string) $item->stock_akhir,
        'Arus' => $item->arus,
        'Keterangan' => $item->keterangan,
      ];
    });
  }

  public function headings(): array
  {
    return [
      'Nomor Faktur',
      'Tanggal',
      'Supplier/Pelanggan',
      'Batch',
      'Expired',
      'Mutasi',
      'Posisi',
      'Arus',
      'Keterangan'
    ];
  }
}
