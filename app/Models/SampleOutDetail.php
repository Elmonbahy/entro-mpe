<?php

namespace App\Models;

use App\Enums\StatusBarangKeluar;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SampleOutDetail extends Model
{
  use HasFactory;

  protected $table = 'sample_out_details';
  protected $fillable = [
    'sample_out_id',
    'barang_id',
    'jumlah_barang_dipesan',
    'jumlah_barang_keluar',
    'batch',
    'tgl_expired',
    'status_barang_keluar',
  ];

  protected $casts = [
    'status_barang_keluar' => StatusBarangKeluar::class,
  ];

  protected static function booted()
  {
    static::deleting(function ($row) {
      $row->samplemutation()->delete();
    });
  }

  /**
   * e.g. $faktur->status_barang_keluar_label
   * */
  public function getStatusBarangKeluarLabelAttribute(): string
  {
    return $this->status_barang_keluar->label();
  }

  public function sampleBarang()
  {
    return $this->belongsTo(SampleBarang::class, 'barang_id', 'barang_id');
  }

  public function sampleout()
  {
    return $this->belongsTo(SampleOut::class, 'sample_out_id');
  }

  public function returs()
  {
    return $this->morphMany(BarangSampleRetur::class, 'returnable');
  }

  public function samplemutation()
  {
    return $this->morphOne(SampleMutation::class, 'mutationable');
  }
}
