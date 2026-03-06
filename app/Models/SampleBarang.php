<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SampleBarang extends Model
{
  /** @use HasFactory<\Database\Factories\SampleBarangFactory> */
  use HasFactory;

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = ['barang_id', 'satuan'];


  public function stocks()
  {
    return $this->hasMany(BarangSampleStock::class);
  }

  public function sampleInDetails()
  {
    return $this->hasMany(SampleInDetail::class, 'barang_id', 'barang_id');
  }

  public function sampleOutDetails()
  {
    return $this->hasMany(SampleOutDetail::class, 'barang_id', 'barang_id');
  }

  public function sampleMutations()
  {
    return $this->hasMany(SampleMutation::class, 'barang_id', 'barang_id');
  }

  public function barang()
  {
    return $this->belongsTo(Barang::class, 'barang_id');
  }

}
