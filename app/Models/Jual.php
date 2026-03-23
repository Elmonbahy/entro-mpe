<?php

namespace App\Models;

use App\Enums\StatusBayar;
use App\Enums\StatusFaktur;
use App\Enums\StatusKirim;
use App\Services\HutangChecker;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Jual extends Model
{
  protected $fillable = [
    'nomor_faktur',
    'nomor_pemesanan',
    'tipe_penjualan',
    'tgl_faktur',
    'status_faktur',
    'status_kirim',
    'diskon_faktur',
    'kredit',
    'ppn',
    'keterangan_faktur',
    'is_pungut_ppn',
    'ongkir',
    'bayar',
    'status_bayar',
    'keterangan_bayar',
    'pelanggan_id',
    'salesman_id',
    'cetak_titip_faktur_at'
  ];

  protected $casts = [
    'bayar' => 'array',
    'status_faktur' => StatusFaktur::class,
    'status_bayar' => StatusBayar::class,
    'status_kirim' => StatusKirim::class,
  ];

  protected static function boot()
  {
    parent::boot();

    // Validasi hutang saat membuat Jual baru
    static::creating(function ($jual) {
      HutangChecker::validateHutang($jual->pelanggan_id);
    });
  }


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
   * e.g. $faktur->total_faktur
   * */
  public function getTotalFakturAttribute()
  {
    return $this->jualDetails->sum('total_tagihan');
  }

  /**
   * e.g. $faktur->total_faktur->untuk tabel jual
   * */
  public function getTotalFakturFormattedAttribute()
  {
    return number_format(round($this->jualDetails->sum('total_tagihan')), 0, ',', '.');
  }

  /**
   * e.g. $faktur->totaldpp
   * */
  public function getTotalDppAttribute()
  {
    return $this->jualDetails->sum('total');
  }

  /**
   * e.g. $faktur->totalppn
   * */
  public function getTotalPpnAttribute()
  {
    return $this->jualDetails->sum('harga_ppn');
  }

  /**
   * e.g. $faktur->total_tagihan
   * */
  public function getTotalTagihanAttribute()
  {
    $total = $this->is_pungut_ppn
      ? $this->jualDetails->sum('total_tagihan')
      : $this->jualDetails->sum('total');

    $ongkir = $this->ongkir ?? 0;

    return round($total + $ongkir);
  }

  /**
   * e.g. $faktur->total_tagihan->untuk di laporan tanpa round
   * */
  public function getTotalTagihanLaporanAttribute()
  {
    $total = $this->is_pungut_ppn
      ? $this->jualDetails->sum('total_tagihan')
      : $this->jualDetails->sum('total');

    $ongkir = $this->ongkir ?? 0;

    return $total + $ongkir;
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
   * e.g. $faktur->sisa_tagihan
   */
  public function getSisaTagihanAttribute()
  {
    return $this->total_tagihan - $this->total_terbayar;
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

  /**
   * e.g. $faktur->status_kirim_label
   * */
  public function getStatusKirimLabelAttribute(): string
  {
    return $this->status_kirim->label();
  }

  /**
   * e.g. $faktur->pungut_ppn
   * */
  public function getPungutPpnAttribute()
  {
    return match ((int) $this->is_pungut_ppn) {
      1 => 'Ya',
      default => 'Tidak',
    };
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

  public function suratJalanDetails()
  {
    return $this->hasManyThrough(
      SuratJalanDetail::class,
      JualDetail::class,
      'jual_id',         // foreign key on JualDetail table
      'jual_detail_id',  // foreign key on SuratJalanDetail table
      'id',              // local key on Jual table
      'id'               // local key on JualDetail table
    );
  }
}
