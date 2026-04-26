<?php

namespace App\Models;

use App\Enums\StatusFaktur;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Beli extends Model
{
  /** @use HasFactory<\Database\Factories\BeliFactory> */
  use HasFactory;

  protected $fillable = [
    'nomor_pemesanan',
    'nomor_faktur',
    'tgl_faktur',
    'tgl_terima_faktur',
    'status_faktur',
    'keterangan_faktur',
    'supplier_id',
  ];

  protected $casts = [
    'status_faktur' => StatusFaktur::class,
  ];

  public static function generateNomorPemesanan($tgl_faktur)
  {
    $date = Carbon::parse($tgl_faktur);
    $tahun = $date->format('Y');
    $bulan = $date->format('m');

    $prefix = "SP/{$tahun}/{$bulan}-";

    // Cari nomor terakhir untuk bulan & tahun tersebut
    $lastFaktur = self::where('nomor_pemesanan', 'like', $prefix . '%')
      ->orderBy('nomor_pemesanan', 'desc')
      ->first();

    $nextNumber = 1;
    if ($lastFaktur) {
      // Ambil 3 digit terakhir dari nomor terakhir
      $lastNumber = (int) substr($lastFaktur->nomor_pemesanan, -3);
      $nextNumber = $lastNumber + 1;
    }

    // Format hasil akhir
    return $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
  }

  /**
   * e.g. $faktur->status_faktur_label
   * */
  public function getStatusFakturLabelAttribute(): string
  {
    return $this->status_faktur->label();
  }

  public function supplier()
  {
    return $this->belongsTo(Supplier::class, 'supplier_id');
  }

  public function beliDetails()
  {
    return $this->hasMany(BeliDetail::class, 'beli_id');
  }

  public function barangReturs()
  {
    return $this->hasManyThrough(
      BarangRetur::class,
      BeliDetail::class,
      'beli_id',       // Foreign key on BeliDetail
      'returnable_id', // Foreign key on BarangRetur
      'id',            // Local key on Beli
      'id'             // Local key on BeliDetail
    )->where('returnable_type', BeliDetail::class);
  }

}
