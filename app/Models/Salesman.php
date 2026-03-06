<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Salesman extends Model
{
  /** @use HasFactory<\Database\Factories\SalesmanFactory> */
  use HasFactory, SoftDeletes;

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = ['kode', 'nama'];

  /**
   * Generate the next available code for kode column
   * @return string
   */
  public static function getNewCode(): string
  {
    $prefix = 'S';

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
