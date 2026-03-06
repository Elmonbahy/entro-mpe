<?php

namespace App\Enums;

enum StatusBayar: string
{
  case UNPAID = 'UNPAID';
  case PAID = 'PAID';

  public function label() : string
  {
    return match ($this) {
      self::UNPAID => 'Belum Lunas',
      self::PAID => 'Lunas',
    };
  }

  public function labelPowergridFilter() : string
  {
    return $this->label();
  }
}
