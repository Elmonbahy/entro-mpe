<?php

namespace App\Models;

use App\Enums\StatusBarangMasuk;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SampleInDetail extends Model
{
  use HasFactory;

  protected $table = 'sample_in_details';
  protected $fillable = [
    'jumlah_barang_dipesan',
    'jumlah_barang_masuk',
    'batch',
    'tgl_expired',
    'status_barang_masuk',
    'sample_in_id',
    'barang_id',
  ];

  protected $casts = [
    'status_barang_masuk' => StatusBarangMasuk::class,
  ];

  protected static function booted()
  {
    static::deleting(function ($row) {
      $row->samplemutation()->delete();
    });
  }

  /**
   * e.g. $sample->status_barang_masuk_label
   * */
  public function getStatusBarangMasukLabelAttribute(): string
  {
    return $this->status_barang_masuk->label();
  }

  public function samplein()
  {
    return $this->belongsTo(SampleIn::class, 'sample_in_id');
  }

  public function sampleBarang()
  {
    return $this->belongsTo(SampleBarang::class, 'barang_id', 'barang_id');
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
