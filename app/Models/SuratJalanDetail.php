<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuratJalanDetail extends Model
{
  protected $fillable = [
    'jumlah_barang_dikirim',
    'surat_jalan_id',
    'jual_detail_id',
  ];

  public function suratJalan()
  {
    return $this->belongsTo(SuratJalan::class);
  }

  public function jualDetail()
  {
    return $this->belongsTo(JualDetail::class);
  }

  public function barang()
  {
    return $this->hasOneThrough(
      Barang::class,
      JualDetail::class,
      'id',        // Foreign key on jual_details
      'id',        // Foreign key on barangs
      'jual_detail_id', // Local key on surat_jalan_details
      'barang_id'  // Local key on jual_details
    );
  }

  public function jual()
  {
    return $this->hasOneThrough(
      Jual::class,
      JualDetail::class,
      'id',        // Foreign key on jual_details
      'id',        // Foreign key on juals
      'jual_detail_id', // Local key on surat_jalan_details
      'jual_id'    // Local key on jual_details
    );
  }
}
