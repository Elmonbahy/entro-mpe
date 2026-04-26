<?php

namespace App\Models;

use App\Enums\StatusBarangKeluar;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JualDetail extends Model
{
  use HasFactory;

  protected $fillable = [
    'jual_id',
    'barang_id',
    'jumlah_barang_dipesan',
    'jumlah_barang_keluar',
    'status_barang_keluar',
    'batch',
    'tgl_expired',
    'keterangan',
    'harga_jual',
  ];

  protected $casts = [
    'status_barang_keluar' => StatusBarangKeluar::class,
  ];

  protected static function booted()
  {
    static::deleting(function ($row) {
      $row->mutation()->delete();
    });
  }

  /**
   * e.g. $faktur->status_barang_keluar_label
   * */
  public function getStatusBarangKeluarLabelAttribute(): string
  {
    return $this->status_barang_keluar->label();
  }


  public function barang()
  {
    return $this->belongsTo(Barang::class);
  }

  public function jual()
  {
    return $this->belongsTo(Jual::class);
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
