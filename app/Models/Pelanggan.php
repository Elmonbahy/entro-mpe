<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pelanggan extends Model
{
  /** @use HasFactory<\Database\Factories\PelangganFactory> */
  use HasFactory;

  protected $fillable = [
    'nama',
    'alamat',
    'kota',
    'contact_person',
    'contact_phone',
    'tipe',
  ];

  public function juals()
  {
    return $this->hasMany(Jual::class);
  }

}
