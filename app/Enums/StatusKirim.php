<?php
namespace App\Enums;

enum StatusKirim: string
{
  case PENDING = 'PENDING';
  case PARTIAL = 'PARTIAL';
  case SHIPPED = 'SHIPPED';

  public function label(): string
  {
    return match ($this) {
      self::PENDING => 'Pending',
      self::PARTIAL => 'Partial',
      self::SHIPPED => 'Shipped',
    };
  }

  public function labelPowergridFilter(): string
  {
    return $this->label();
  }
}
