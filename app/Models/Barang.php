<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
  /** @use HasFactory<\Database\Factories\BarangFactory> */
  use HasFactory;

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = ['kode', 'nama', 'satuan', 'nie', 'harga_jual_pemerintah', 'harga_jual_swasta', 'harga_beli', 'group_id', 'brand_id', 'supplier_id', 'kegunaan'];


  public function stocks()
  {
    return $this->hasMany(BarangStock::class);
  }

  public function barang_stocks()
  {
    return $this->hasMany(BarangStock::class);
  }

  public function beli_details()
  {
    return $this->hasMany(BeliDetail::class);
  }

  public function jual_details()
  {
    return $this->hasMany(JualDetail::class);
  }

  public function group()
  {
    return $this->belongsTo(Group::class, 'group_id');
  }

  public function brand()
  {
    return $this->belongsTo(Brand::class, 'brand_id');
  }

  public function supplier()
  {
    return $this->belongsTo(Supplier::class, 'supplier_id');
  }
  public function mutations()
  {
    return $this->hasMany(Mutation::class, 'barang_id', 'id');
  }

}
