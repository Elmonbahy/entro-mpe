<?php

namespace App\Enums;

enum JenisFiskal: string
{
  case NORMAL = 'NORMAL';
  case SPLIT = 'SPLIT';
  case GABUNGAN = 'GABUNGAN';
  case PERALIHAN = 'PERALIHAN';

  public function label(): string
  {
    return match ($this) {
      self::NORMAL => 'Normal',
      self::SPLIT => 'Pecah (Split)',
      self::GABUNGAN => 'Gabungan (Consolidated)',
      self::PERALIHAN => 'Peralihan (Legacy)',
    };
  }

  public function color(): string
  {
    return match ($this) {
      self::NORMAL => 'success',
      self::SPLIT => 'warning',
      self::GABUNGAN => 'info',
      self::PERALIHAN => 'primary',
    };
  }

  public function labelPowergridFilter(): string
  {
    return $this->label();
  }

  public static function toObjectArray(): array
  {
    return array_map(
      fn($case) => (object) [
        'key' => $case->value,
        'value' => $case->label()
      ],
      self::cases()
    );
  }
}
