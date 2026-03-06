<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
  /** @use HasFactory<\Database\Factories\SupplierFactory> */
  use HasFactory;

  protected $fillable = ['kode', 'nama', 'alamat', 'kota', 'npwp', 'contact_person', 'contact_phone'];

  /**
   * Generate the next available code for kode column
   * @return string
   */
  public static function getNewCode(): string
  {
    $prefix = 'SUPP';

    $lastRecord = self::latest('id')->first();

    if (! $lastRecord) {
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

  public function barangs()
  {
    return $this->hasMany(Barang::class);
  }

  public function belis()
  {
    return $this->hasMany(Beli::class);
  }
}
