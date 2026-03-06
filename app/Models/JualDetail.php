<?php

namespace App\Models;

use App\Enums\StatusBarangKeluar;
use App\Enums\StatusKirim;
use App\Services\SuratJalanService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JualDetail extends Model
{
  use HasFactory;

  protected $fillable = [
    'jual_id',
    'barang_id',
    'jumlah_barang_dipesan',
    'jumlah_barang_keluar',
    'status_barang_keluar',
    'batch',
    'tgl_expired',
    'diskon1',
    'diskon2',
    'keterangan',
    'harga_jual',
  ];

  protected $casts = [
    'status_barang_keluar' => StatusBarangKeluar::class,
    'status_kirim' => StatusKirim::class,
  ];

  protected static function booted()
  {
    static::deleting(function ($row) {
      $row->mutation()->delete();
    });

    static::created(function ($jualDetail) {
      (new SuratJalanService())->updateStatusKirimByJual($jualDetail->jual_id);
    });

    static::updated(function ($jualDetail) {
      (new SuratJalanService())->updateStatusKirimByJual($jualDetail->jual_id);
    });
  }

  /**
   * e.g. $faktur->sub_nilai
   * */
  public function getSubNilaiAttribute()
  {
    return $this->jumlah_barang_dipesan * $this->harga_jual;
  }

  /**
   * e.g. $faktur->harga_diskon1
   * */
  public function getHargaDiskon1Attribute()
  {
    return $this->sub_nilai * (floatVal($this->diskon1) / 100);
  }

  /**
   * e.g. $faktur->nilai_diskon1
   * */
  public function getNilaiDiskon1Attribute()
  {
    return $this->sub_nilai - $this->harga_diskon1;
  }

  /**
   * e.g. $faktur->harga_diskon2
   * */
  public function getHargaDiskon2Attribute()
  {
    return $this->nilai_diskon1 * (floatVal($this->diskon2) / 100);
  }

  /**
   * e.g. $faktur->total
   * */
  public function getTotalAttribute()
  {
    return $this->nilai_diskon1 - $this->harga_diskon2;
  }

  /**
   * e.g. $faktur->harga_ppn
   * */
  public function getHargaPpnAttribute()
  {
    return $this->total * ($this->jual->ppn / 100);
  }

  /**
   * e.g. $faktur->total_tagihan
   * */
  public function getTotalTagihanAttribute()
  {
    return $this->total + $this->harga_ppn;
  }

  /**
   * e.g. $faktur->harga_diskon_faktur
   * */
  public function getHargaDiskonFakturAttribute()
  {
    return $this->total_tagihan + ($this->jual->diskon_faktur / 100);
  }

  /**
   * e.g. $faktur->total_diskon_faktur
   * */
  public function getTotalDiskonFakturAttribute()
  {
    return $this->total_tagihan - $this->harga_diskon_faktur;
  }


  /**
   * e.g. $faktur->status_barang_keluar_label
   * */
  public function getStatusBarangKeluarLabelAttribute(): string
  {
    return $this->status_barang_keluar->label();
  }


  public function barang()
  {
    return $this->belongsTo(Barang::class);
  }

  public function jual()
  {
    return $this->belongsTo(Jual::class);
  }

  public function suratJalanDetails()
  {
    return $this->hasMany(SuratJalanDetail::class);
  }

  public function returs()
  {
    return $this->morphMany(BarangRetur::class, 'returnable');
  }

  public function mutation()
  {
    return $this->morphOne(Mutation::class, 'mutationable');
  }
}
