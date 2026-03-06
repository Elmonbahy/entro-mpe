<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
  /** @use HasFactory<\Database\Factories\GroupFactory> */
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
