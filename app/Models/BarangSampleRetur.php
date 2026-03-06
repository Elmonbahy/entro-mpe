<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarangSampleRetur extends Model
{
  protected $fillable = ['jumlah_barang_retur', 'keterangan', 'is_diganti', 'barang_id', 'diganti_at'];

  public function returnable()
  {
    return $this->morphTo();
  }

  public function sampleBarang()
  {
    return $this->belongsTo(SampleBarang::class, 'barang_id', 'barang_id');
  }
}
