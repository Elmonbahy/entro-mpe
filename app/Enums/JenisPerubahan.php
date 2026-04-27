<?php

namespace App\Enums;

enum JenisPerubahan: string
{
  case AWAL = 'AWAL';
  case TAMBAH = 'TAMBAH';
  case KURANG = 'KURANG';

  public function label(): string
  {
    return match ($this) {
      self::AWAL => 'Awal Stock',
      self::TAMBAH => 'Penambahan',
      self::KURANG => 'Pengurangan',
    };
  }

  public function labelPowergridFilter(): string
  {
    return $this->label();
  }

  public static function toObjectArray(): array
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
