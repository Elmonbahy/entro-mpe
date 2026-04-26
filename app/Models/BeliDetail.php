<?php

namespace App\Models;

use App\Enums\StatusBarangMasuk;
use Illuminate\Database\Eloquent\Model;

class BeliDetail extends Model
{
  protected $fillable = [
    'beli_id',
    'barang_id',
    'jumlah_barang_dipesan',
    'jumlah_barang_masuk',
    'status_barang_masuk',
    'batch',
    'tgl_expired',
    'keterangan',
    'harga_beli',
  ];

  protected $casts = [
    'status_barang_masuk' => StatusBarangMasuk::class,
  ];

  protected static function booted()
  {
    static::deleting(function ($row) {
      $row->mutation()->delete();
    });
  }

  /**
   * e.g. $faktur->status_barang_masuk_label
   * */
  public function getStatusBarangMasukLabelAttribute(): string
  {
    return $this->status_barang_masuk->label();
  }

  public function beli()
  {
    return $this->belongsTo(Beli::class, 'beli_id');
  }

  public function barang()
  {
    return $this->belongsTo(Barang::class, 'barang_id');
  }

  public function returs()
  {
    return $this->morphMany(BarangRetur::class, 'returnable');
  }
  public function mutation()
  {
    return $this->morphOne(Mutation::class, 'mutationable');
  }
}
