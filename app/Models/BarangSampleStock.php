<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BarangSampleStock extends Model
{
  use HasFactory;

  protected $table = 'barang_sample_stocks';
  protected $fillable = [
    'barang_id',
    'jumlah_stock',
    'batch',
    'tgl_expired',
  ];

  public function sampleBarang()
  {
    return $this->belongsTo(SampleBarang::class, 'barang_id', 'barang_id');
  }
}
