<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarangStock extends Model
{
  protected $fillable = ['barang_id', 'jumlah_stock', 'batch', 'tgl_expired'];

  public function barang()
  {
    return $this->belongsTo(Barang::class);
  }
}
