<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\StatusSample;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SampleIn extends Model
{
  use HasFactory;

  protected $table = 'sample_ins';
  protected $fillable = [
    'nomor_sample',
    'tanggal',
    'keterangan',
    'status_sample',
    'supplier_id',
  ];

  protected $casts = [
    'status_sample' => StatusSample::class,
  ];

  public static function generateNomorSample($tanggal, $id)
  {
    $date = Carbon::parse($tanggal);
    $tahun = $date->format('Y');
    $bulan = $date->format('m');

    // Format: SMPL/202510/0005 (nomor belakang dari ID)
    return 'APM/SAMP/IN/' . $tahun . '-' . $bulan . '-' . str_pad($id, 3, '0', STR_PAD_LEFT);
  }

  /**
   * e.g. $sample->status_sample_label
   * */
  public function getStatusSampleLabelAttribute(): string
  {
    return $this->status_sample->label();
  }

  public function supplier()
  {
    return $this->belongsTo(Supplier::class, 'supplier_id');
  }

  public function sampleinDetails()
  {
    return $this->hasMany(SampleInDetail::class, 'sample_in_id');
  }

}
