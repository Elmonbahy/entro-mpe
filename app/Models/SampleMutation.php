<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SampleMutation extends Model
{
  use HasFactory;

  protected $table = 'sample_mutations';
  protected $fillable = [
    'stock_awal',
    'stock_masuk',
    'stock_keluar',
    'stock_retur_keluar',
    'stock_retur_masuk',
    'stock_rusak',
    'stock_akhir',
    'barang_id',
    'batch',
    'tgl_expired',
    'tgl_mutation'
  ];

  public function getArusAttribute()
  {
    if ($this->mutation_type === 'SampleOutDetail') {
      return $this->stock_retur_keluar ? 'Retur-Keluar' : 'Keluar';
    }

    if ($this->mutation_type === 'SampleInDetail') {
      return $this->stock_retur_masuk ? 'Retur-Masuk' : 'Masuk';
    }


    if ($this->mutation_type === 'BarangSampleRusak') {
      $barangsampleRusak = $this->mutationable;

      if ($barangsampleRusak && $barangsampleRusak->penyebab instanceof PenyebabBarangRusak) {
        return $barangsampleRusak->penyebab->label();
      }

      return 'Barang Rusak';
    }

    return 'Tidak Diketahui';
  }

  public function getMutasiAttribute()
  {
    if ($this->mutation_type === 'SampleOutDetail') {
      return $this->stock_retur_keluar ? $this->stock_retur_keluar : "- $this->stock_keluar";
    }

    if ($this->mutation_type === 'SampleInDetail') {
      return $this->stock_retur_masuk ? "- $this->stock_retur_masuk" : $this->stock_masuk;
    }

    if ($this->mutation_type === 'BarangSampleRusak') {
      return $this->stock_masuk ? $this->stock_masuk : "- $this->stock_rusak";
    }
  }

  public function getExpiredAttribute()
  {
    $expired = null;

    if ($this->mutation_type === 'BarangSampleRusak') {
      $expired = optional($this->mutationable?->barangsampleStock)->tgl_expired;
    } else {
      $expired = optional($this->mutationable)->tgl_expired;
    }

    return $expired ? \Carbon\Carbon::parse($expired)->format('d/m/Y') : '-';
  }

  public function getUserAttribute()
  {
    if ($this->mutation_type === 'SampleOutDetail') {
      return $this->mutationable->sampleout->pelanggan->nama;
    }

    if ($this->mutation_type === 'SampleInDetail') {
      return $this->mutationable->samplein->supplier->nama;
    }

    return '-';
  }

  public function getNomorSampleAttribute()
  {
    if ($this->mutation_type === 'SampleOutDetail') {
      return $this->mutationable->sampleout->nomor_sample;
    }

    if ($this->mutation_type === 'SampleInDetail') {
      return $this->mutationable->samplein->nomor_sample;
    }

    return 'Tidak Diketahui';
  }

  public function getMutationTypeAttribute()
  {
    return class_basename($this->mutationable_type);
  }

  public function mutationable()
  {
    return $this->morphTo();
  }

  public function sampleBarang()
  {
    return $this->belongsTo(SampleBarang::class, 'barang_id', 'barang_id');
  }

}
