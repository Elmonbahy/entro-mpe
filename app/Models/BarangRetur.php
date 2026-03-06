<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarangRetur extends Model
{
  protected $fillable = ['jumlah_barang_retur', 'keterangan', 'is_diganti', 'barang_id', 'diganti_at'];

  public function returnable()
  {
    return $this->morphTo();
  }

  public function barang()
  {
    return $this->belongsTo(Barang::class);
  }
}
