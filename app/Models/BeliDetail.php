<?php

namespace App\Models;

use App\Enums\StatusBarangMasuk;
use Illuminate\Database\Eloquent\Model;

class BeliDetail extends Model
{
  protected $fillable = [
    'beli_id',
    'barang_id',
    'jumlah_barang_dipesan',
    'jumlah_barang_masuk',
    'status_barang_masuk',
    'batch',
    'tgl_expired',
    'diskon1',
    'diskon2',
    'keterangan',
    'harga_beli',
  ];

  protected $casts = [
    'status_barang_masuk' => StatusBarangMasuk::class,
  ];

  public static function getHnaBeli(int $barang_id)
  {
    $details = self::where('barang_id', $barang_id)->get();
    $totalJumlahBarangMasuk = $details->sum('jumlah_barang_masuk');
    $totalTagihan = $details->sum(fn ($detail) => $detail->total_tagihan);

    return $totalJumlahBarangMasuk === 0 ? 0 : $totalTagihan / $totalJumlahBarangMasuk;
  }

  protected static function booted()
  {
    static::deleting(function ($row) {
      $row->mutation()->delete();
    });
  }

  /**
   * e.g. $faktur->sub_nilai
   * */
  public function getSubNilaiAttribute()
  {
    return $this->jumlah_barang_dipesan * $this->harga_beli;
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
    return $this->total * ($this->beli->ppn / 100);
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
    return $this->total_tagihan + ($this->beli->diskon_faktur / 100);
  }

  /**
   * e.g. $faktur->total_diskon_faktur
   * */
  public function getTotalDiskonFakturAttribute()
  {
    return $this->total_tagihan - $this->harga_diskon_faktur;
  }

  /**
   * e.g. $faktur->status_barang_masuk_label
   * */
  public function getStatusBarangMasukLabelAttribute(): string
  {
    return $this->status_barang_masuk->label();
  }

  public function beli()
  {
    return $this->belongsTo(Beli::class, 'beli_id');
  }

  public function barang()
  {
    return $this->belongsTo(Barang::class, 'barang_id');
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
