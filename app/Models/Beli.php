<?php

namespace App\Models;

use App\Enums\StatusBayar;
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
    'diskon_faktur',
    'kredit',
    'ppn',
    'ongkir',
    'materai',
    'biaya_lainnya',
    'status_faktur',
    'keterangan_faktur',
    'bayar',
    'status_bayar',
    'keterangan_bayar',
    'supplier_id',
  ];

  protected $casts = [
    'bayar' => 'array',
    'status_faktur' => StatusFaktur::class,
    'status_bayar' => StatusBayar::class,
  ];

  // untuk update tgl faktur di beli di mutasi mengikuti
  protected static function boot()
  {
    parent::boot();

    // static::updated(function ($beli) {
    //   if ($beli->isDirty('tgl_terima_faktur')) {
    //     foreach ($beli->beliDetails as $beliDetail) {
    //       $beliDetail->mutation()->update([
    //         'tgl_mutation' => $beli->tgl_terima_faktur
    //       ]);
    //     }
    //   }
    // });
  }

  public static function generateNomorPemesanan($tgl_faktur)
  {
    $date = Carbon::parse($tgl_faktur);
    $tahun = $date->format('Y');
    $bulan = $date->format('m');

    $prefix = "SPB/APM/{$tahun}/{$bulan}-";

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
   * e.g. $faktur->total_faktur
   * */
  public function getTotalFakturAttribute()
  {
    return $this->beliDetails->sum('total_tagihan');
  }

  /**
   * e.g. $faktur->total_faktur->untuk tabel beli
   * */
  public function getTotalFakturFormattedAttribute()
  {
    return number_format(round($this->beliDetails->sum('total_tagihan')), 0, ',', '.');
  }

  /**
   * e.g. $faktur->totaldpp
   * */
  public function getTotalDppAttribute()
  {
    return $this->beliDetails->sum('total');
  }

  /**
   * e.g. $faktur->totalppn
   * */
  public function getTotalPpnAttribute()
  {
    return $this->beliDetails->sum('harga_ppn');
  }


  /**
   * e.g. $faktur->total_tagihan
   * */
  public function getTotalTagihanAttribute()
  {
    $total_tagihan = $this->beliDetails->sum('total_tagihan');
    $ongkir = $this->ongkir ?? 0;
    $materai = $this->materai ?? 0;
    $biaya_lainnya = $this->biaya_lainnya ?? 0;

    return round($total_tagihan + $ongkir + $materai + $biaya_lainnya);
  }

  /**
   * e.g. $faktur->total_tagihan->untuk di laporan tanpa round
   * */
  public function getTotalTagihanLaporanAttribute()
  {
    $total_tagihan = $this->beliDetails->sum('total_tagihan');
    $ongkir = $this->ongkir ?? 0;
    $materai = $this->materai ?? 0;
    $biaya_lainnya = $this->biaya_lainnya ?? 0;

    return $total_tagihan + $ongkir + $materai + $biaya_lainnya;
  }


  /**
   * e.g. $faktur->sisa_tagihan
   */
  public function getSisaTagihanAttribute()
  {
    return $this->total_tagihan - $this->total_terbayar;
  }

  /**
   * Accessor for status_bayar label
   * e.g. $faktur->total_terbayar
   * */
  public function getTotalTerbayarAttribute()
  {
    if (!$this->bayar) {
      return 0;
    }

    $total = array_sum(array_column($this->bayar, 'terbayar'));
    return $total;
  }

  /**
   * e.g. $faktur->status_bayar_label
   * */
  public function getStatusBayarLabelAttribute(): string
  {
    return $this->status_bayar->label();
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
