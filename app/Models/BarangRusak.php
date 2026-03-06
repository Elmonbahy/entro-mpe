<?php

namespace App\Models;

use App\Enums\PenyebabBarangRusak;
use App\Enums\TindakanBarangRusak;
use Illuminate\Database\Eloquent\Model;

class BarangRusak extends Model
{
  protected $fillable = [
    'penyebab',
    'jumlah_barang_rusak',
    'tindakan',
    'keterangan',
    'barang_stock_id',
    'tgl_rusak'
  ];

  protected $casts = [
    'penyebab' => PenyebabBarangRusak::class,
    'tindakan' => TindakanBarangRusak::class,
  ];

  public function barangStock()
  {
    return $this->belongsTo(BarangStock::class);
  }

  public function barang()
  {
    return $this->hasOneThrough(
      Barang::class,
      BarangStock::class,
      'id',        // Foreign key on barang stock
      'id',        // Foreign key on barangs
      'barang_stock_id', // Local key on barang rusak
      'barang_id'  // Local key on barang stock
    );
  }

  public function mutation()
  {
    return $this->morphOne(Mutation::class, 'mutationable');
  }
}
