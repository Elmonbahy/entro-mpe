<?php

namespace App\Enums;

enum PenyebabBarangRusak: string
{
  case RUSAK = 'RUSAK';
  case EXPIRED = 'EXPIRED';

  public function label() : string
  {
    return match ($this) {
      self::RUSAK => 'Rusak',
      self::EXPIRED => 'Expired',
    };
  }

  public function labelPowergridFilter() : string
  {
    return $this->label();
  }

  /**
   * Convert all enum cases to an array of objects.
   *
   * Example output:
   * [
   *     (object) ['key' => 'RUSAK', 'value' => 'Rusak'],
   *     (object) ['key' => 'EXPIRED', 'value' => 'Expired'],
   * ]
   */
  public static function toObjectArray() : array
  {
    return array_map(
      fn ($case) => (object) [
        'key' => $case->value,
        'value' => $case->label()
      ],
      self::cases()
    );
  }
}
