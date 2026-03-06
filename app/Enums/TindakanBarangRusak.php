<?php

namespace App\Enums;

enum TindakanBarangRusak: string
{
  case DIMUSNAKAN = 'DIMUSNAKAN';
  case DIGANTI = 'DIGANTI';

  public function label() : string
  {
    return match ($this) {
      self::DIMUSNAKAN => 'Dimusnakan',
      self::DIGANTI => 'Diganti',
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
   *     (object) ['key' => 'DIMUSNAKAN', 'value' => 'Dimusnakan'],
   *     (object) ['key' => 'DIGANTI', 'value' => 'Diganti'],
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
