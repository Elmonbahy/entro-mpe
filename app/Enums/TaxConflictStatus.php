<?php

namespace App\Enums;

enum TaxConflictStatus: int
{
  case MATCHED = 0;
  case CONFLICT = 1;

  public function label(): string
  {
    return match ($this) {
      self::MATCHED => 'Sinkron',
      self::CONFLICT => 'Butuh Revisi',
    };
  }

  public function color(): string
  {
    return match ($this) {
      self::MATCHED => 'text-success bg-light-success border-success',
      self::CONFLICT => 'text-danger bg-light-danger border-danger shadow-sm',
    };
  }

  public function icon(): string
  {
    return match ($this) {
      self::MATCHED => 'bi-check-circle-fill',
      self::CONFLICT => 'bi-exclamation-triangle-fill animate-pulse',
    };
  }
}
