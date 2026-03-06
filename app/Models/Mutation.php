<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\JenisPerubahan;
use App\Enums\PenyebabBarangRusak;

/**
 * Digunakan untuk mencatat transaksi:
 * - BarangRusak: create & delete
 * - BarangStockAwal: create & delete
 * - BeliDetail: Create
 * - JualDetail: Create
 */
class Mutation extends Model
{
  protected $fillable = [
    'stock_awal',
    'stock_masuk',
    'stock_keluar',
    'stock_retur_jual',
    'stock_retur_beli',
    'stock_rusak',
    'stock_akhir',
    'barang_id',
    'batch',
    'tgl_expired',
    'tgl_mutation'
  ];

  public function getDiscAttribute()
  {
    if (in_array($this->mutation_type, ['JualDetail', 'BeliDetail'])) {
      return [
        'diskon1' => $this->mutationable->diskon1,
        'diskon2' => $this->mutationable->diskon2,
      ];
    }

    return [
      'diskon1' => '-',
      'diskon2' => '-',
    ];
  }

  public function getHargaAttribute()
  {
    if ($this->mutation_type === 'JualDetail') {
      $nilai = \Number::currency($this->mutationable->harga_jual, 'IDR', 'id_ID');
      return "(Jual) $nilai";
    }

    if ($this->mutation_type === 'BeliDetail') {
      $nilai = \Number::currency($this->mutationable->harga_beli, 'IDR', 'id_ID');
      return "(Beli) $nilai";
    }
    return '-';
  }

  public function getArusAttribute()
  {
    if ($this->mutation_type === 'JualDetail') {
      return $this->stock_retur_jual ? 'Retur-Jual' : 'Keluar';
    }

    if ($this->mutation_type === 'BeliDetail') {
      return $this->stock_retur_beli ? 'Retur-Beli' : 'Masuk';
    }

    if ($this->mutation_type === 'BarangStockAwal') {
      $barangStockAwal = $this->mutationable;

      if ($barangStockAwal && $barangStockAwal->jenis_perubahan instanceof JenisPerubahan) {
        return $barangStockAwal->jenis_perubahan->label();
      }

      return 'Perubahan';
    }

    if ($this->mutation_type === 'BarangRusak') {
      $barangRusak = $this->mutationable;

      if ($barangRusak && $barangRusak->penyebab instanceof PenyebabBarangRusak) {
        return $barangRusak->penyebab->label();
      }

      return 'Barang Rusak';
    }

    return 'Tidak Diketahui';
  }

  public function getMutasiAttribute()
  {
    if ($this->mutation_type === 'JualDetail') {
      return $this->stock_retur_jual ? $this->stock_retur_jual : "- $this->stock_keluar";
    }

    if ($this->mutation_type === 'BeliDetail') {
      return $this->stock_retur_beli ? "- $this->stock_retur_beli" : $this->stock_masuk;
    }

    if ($this->mutation_type === 'BarangStockAwal') {
      return $this->stock_keluar ? "- $this->stock_keluar" : $this->stock_masuk;
    }

    if ($this->mutation_type === 'BarangRusak') {
      return $this->stock_masuk ? $this->stock_masuk : "- $this->stock_rusak";
    }
  }

  public function getExpiredAttribute()
  {
    $expired = null;

    if ($this->mutation_type === 'BarangRusak') {
      $expired = optional($this->mutationable?->barangStock)->tgl_expired;
    } else {
      $expired = optional($this->mutationable)->tgl_expired;
    }

    return $expired ? \Carbon\Carbon::parse($expired)->format('d/m/Y') : '-';
  }


  public function getUserAttribute()
  {
    if ($this->mutation_type === 'JualDetail') {
      return $this->mutationable->jual->pelanggan->nama;
    }

    if ($this->mutation_type === 'BeliDetail') {
      return $this->mutationable->beli->supplier->nama;
    }

    return '-';
  }

  public function getNomorFakturAttribute()
  {
    if ($this->mutation_type === 'JualDetail') {
      return $this->mutationable->jual->nomor_faktur;
    }

    if ($this->mutation_type === 'BeliDetail') {
      return $this->mutationable->beli->nomor_faktur;
    }

    if ($this->mutation_type === 'BarangStockAwal') {
      return 'Penyesuaian';
    }

    if ($this->mutation_type === 'BarangRusak') {
      $barangRusak = $this->mutationable;

      if ($barangRusak && $barangRusak->penyebab instanceof PenyebabBarangRusak) {
        return 'Barang ' . $barangRusak->penyebab->label();
      }

      return 'Barang Rusak';
    }

    return 'Tidak Diketahui';
  }

  public function getNomorFakturUrlFor(User $user): ?string
  {
    $prefix = $user->getRoutePrefix();

    if ($this->mutation_type === 'JualDetail') {
      return route("{$prefix}.jual.show", $this->mutationable->jual->id);
    }

    if ($this->mutation_type === 'BeliDetail') {
      return route("{$prefix}.beli.show", $this->mutationable->beli->id);
    }

    return null; // Untuk BarangStockAwal dan BarangRusak, tidak perlu link
  }

  public function getKeteranganAttribute()
  {
    if ($this->mutation_type === 'JualDetail') {
      if ($this->stock_retur_jual) {
        return $this->mutationable->returs->first()->keterangan ?? '-';
      }

      return $this->mutationable->jual->keterangan_faktur ?? '-';
    }

    if ($this->mutation_type === 'BeliDetail') {
      if ($this->stock_retur_beli) {
        return $this->mutationable->returs->first()->keterangan ?? '-';
      }

      return $this->mutationable->beli->keterangan_faktur ?? '-';
    }

    if ($this->mutation_type === 'BarangRusak') {
      return optional($this->mutationable)->keterangan ?? '-';
    }

    if ($this->mutation_type === 'BarangStockAwal') {
      return optional($this->mutationable)->keterangan ?? '-';
    }

    return '-';
  }

  public function getMutationTypeAttribute()
  {
    return class_basename($this->mutationable_type);
  }

  public function mutationable()
  {
    return $this->morphTo();
  }

  public function barang()
  {
    return $this->belongsTo(Barang::class);
  }
}
