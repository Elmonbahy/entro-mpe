<?php

namespace App\Models;

use App\Enums\StatusFaktur;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Jual extends Model
{
  protected $fillable = [
    'nomor_faktur',
    'nomor_pemesanan',
    'tgl_faktur',
    'status_faktur',
    'keterangan_faktur',
    'pelanggan_id',
    'salesman_id',
  ];

  protected $casts = [
    'status_faktur' => StatusFaktur::class,
  ];

  public static function generateNomorFaktur($tgl_faktur)
  {
    // Ambil tahun dan bulan dari tgl_faktur
    $date = Carbon::parse($tgl_faktur);
    $tahun = $date->format('Y');
    $bulan = $date->format('m');

    // Cari nomor terakhir di bulan & tahun yang sama
    $lastFaktur = self::whereYear('tgl_faktur', $tahun)
      ->whereMonth('tgl_faktur', $bulan)
      ->orderBy('nomor_faktur', 'desc')
      ->first();

    // Tentukan nomor urut berikutnya
    $nextNumber = 1;
    if ($lastFaktur) {
      $lastNumber = (int) substr($lastFaktur->nomor_faktur, -3);
      $nextNumber = $lastNumber + 1;
    }

    // Format nomor faktur: YYYYMM-0001
    return $tahun . '-' . $bulan . '-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
  }

  /**
   * e.g. $faktur->status_faktur_label
   * */
  public function getStatusFakturLabelAttribute(): string
  {
    return $this->status_faktur->label();
  }

  public function pelanggan()
  {
    return $this->belongsTo(Pelanggan::class, 'pelanggan_id');
  }

  public function salesman()
  {
    return $this->belongsTo(Salesman::class)->withTrashed();
  }

  public function jualDetails()
  {
    return $this->hasMany(JualDetail::class);
  }

  public function barangReturs()
  {
    return $this->hasManyThrough(
      BarangRetur::class,
      JualDetail::class,
      'jual_id',       // Foreign key on JualDetail
      'returnable_id', // Foreign key on BarangRetur
      'id',            // Local key on Jual
      'id'             // Local key on JualDetail
    )->where('returnable_type', JualDetail::class);
  }
}
