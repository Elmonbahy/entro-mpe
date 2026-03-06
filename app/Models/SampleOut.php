<?php

namespace App\Models;

use App\Enums\StatusSample;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class SampleOut extends Model
{
  use HasFactory;

  protected $table = 'sample_outs';
  protected $fillable = [
    'nomor_sample',
    'tanggal',
    'keterangan',
    'status_sample',
    'pelanggan_id',
    'salesman_id',
  ];

  protected $casts = [
    'status_sample' => StatusSample::class,
  ];

  public static function generateNomorSample($tanggal)
  {
    // Ambil tahun dan bulan dari tanggal
    $date = Carbon::parse($tanggal);
    $tahun = $date->format('Y');
    $bulan = $date->format('m');

    // Cari nomor terakhir di bulan & tahun yang sama
    $lastSampel = self::whereYear('tanggal', $tahun)
      ->whereMonth('tanggal', $bulan)
      ->orderBy('nomor_sample', 'desc')
      ->first();

    // Tentukan nomor urut berikutnya
    $nextNumber = 1;
    if ($lastSampel) {
      $lastNumber = (int) substr($lastSampel->nomor_sample, -3);
      $nextNumber = $lastNumber + 1;
    }

    return 'APM/SAMP/OUT/' . $tahun . '-' . $bulan . '-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
  }

  /**
   * e.g. $sample->status_sample_label
   * */
  public function getStatusSampleLabelAttribute(): string
  {
    return $this->status_sample->label();
  }

  public function pelanggan()
  {
    return $this->belongsTo(Pelanggan::class, 'pelanggan_id');
  }

  public function salesman()
  {
    return $this->belongsTo(Salesman::class);
  }

  public function sampleoutDetails()
  {
    return $this->hasMany(SampleOutDetail::class, 'sample_out_id');
  }
}
