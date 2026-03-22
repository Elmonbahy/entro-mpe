<?php

namespace App\Models;

use App\Enums\StatusRetur;
use Illuminate\Database\Eloquent\Model;

class BarangRetur extends Model
{
  protected $fillable = [
    'jumlah_barang_retur',
    'keterangan',
    'is_diganti',
    'barang_id',
    'diganti_at',
    'status',
    'keterangan_gudang',
    'verified_at'
  ];

  protected $casts = [
    'status' => StatusRetur::class,
  ];

  public function returnable()
  {
    return $this->morphTo();
  }

  public function barang()
  {
    return $this->belongsTo(Barang::class);
  }
}
