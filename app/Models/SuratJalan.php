<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class SuratJalan extends Model
{
  protected $fillable = [
    'tgl_surat_jalan',
    'tgl_kembali_surat_jalan',
    'nomor_surat_jalan',
    'koli',
    'staf_logistik',
    'keterangan',
    'pelanggan_id',
    'alamat_kirim',
    'kota',
    'contact_phone',
    'contact_person',
    'kendaraan_id'
  ];


  /**
   * Generate a unique nomor_surat_jalan
   *
   * @return string
   */
  public static function generateNomorSuratJalan($tgl_surat_jalan): string
  {
    // Ambil tahun dan bulan dari tgl_faktur
    $date = Carbon::parse($tgl_surat_jalan);
    $tahun = $date->format('Y');
    $bulan = $date->format('m');

    // Cari nomor terakhir di bulan & tahun yang sama
    $lastSuratJalan = self::whereYear('tgl_surat_jalan', $tahun)
      ->whereMonth('tgl_surat_jalan', $bulan)
      ->orderBy('nomor_surat_jalan', 'desc')
      ->first();

    // Tentukan nomor urut berikutnya
    $nextNumber = 1;
    if ($lastSuratJalan) {
      $lastNumber = (int) substr($lastSuratJalan->nomor_surat_jalan, -3);
      $nextNumber = $lastNumber + 1;
    }

    // Format nomor faktur: YYYYMM-0001
    return 'SJ/' . 'APM/' . $tahun . '-' . $bulan . '-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
  }

  public function kendaraan()
  {
    return $this->belongsTo(Kendaraan::class);
  }

  public function pelanggan()
  {
    return $this->belongsTo(Pelanggan::class);
  }
  public function suratJalanDetails()
  {
    return $this->hasMany(SuratJalanDetail::class, 'surat_jalan_id');
  }

}
