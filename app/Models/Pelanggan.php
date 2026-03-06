<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pelanggan extends Model
{
  /** @use HasFactory<\Database\Factories\PelangganFactory> */
  use HasFactory;

  protected $fillable = [
    'kode',
    'nama',
    'alamat',
    'kota',
    'npwp',
    'contact_person',
    'contact_phone',
    'rayon',
    'tipe',
    'tipe_harga',
    'area',
    'plafon_hutang',
    'limit_hari',
    'tipe2'
  ];

  /**
   * Generate the next available code for kode column
   * @return string
   */
  public static function getNewCode(): string
  {
    $prefix = 'CUST';

    $lastRecord = self::latest('id')->first();

    if (!$lastRecord) {
      return "{$prefix}001";
    }

    // Ekstrak nomor urut terakhir
    $numericPart = intval(substr($lastRecord->kode, strlen($prefix)));

    // Increment nomor urut
    $newNumericPart = $numericPart + 1;

    // Gunakan padding sesuai dengan panjang nomor urut, minimal 3 digit
    $paddingLength = max(3, strlen((string) $newNumericPart));
    return $prefix . str_pad($newNumericPart, $paddingLength, '0', STR_PAD_LEFT);
  }

  public function juals()
  {
    return $this->hasMany(Jual::class);
  }

}
