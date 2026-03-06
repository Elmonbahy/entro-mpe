<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Brand extends Model
{
  /** @use HasFactory<\Database\Factories\BrandFactory> */
  use HasFactory;

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = ['nama'];

  public function barangs()
  {
    return $this->hasMany(Barang::class);
  }
}
