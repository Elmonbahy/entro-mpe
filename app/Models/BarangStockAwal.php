<?php

namespace App\Models;

use App\Enums\JenisPerubahan;
use Illuminate\Database\Eloquent\Model;

class BarangStockAwal extends Model
{
  protected $fillable = ['barang_id', 'tgl_stock', 'jumlah_stock', 'batch', 'tgl_expired', 'jenis_perubahan', 'keterangan'];

  protected $casts = [
    'jenis_perubahan' => JenisPerubahan::class,
  ];

  public function barang()
  {
    return $this->belongsTo(Barang::class);
  }
  public function mutation()
  {
    return $this->morphOne(Mutation::class, 'mutationable');
  }
}
