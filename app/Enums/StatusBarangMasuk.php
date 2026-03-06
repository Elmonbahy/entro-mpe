<?php
namespace App\Enums;

enum StatusBarangMasuk: string
{
  case BELUM_LENGKAP = 'BELUM_LENGKAP';
  case LENGKAP = 'LENGKAP';
  public function label(): string
  {
    return match ($this) {
      self::BELUM_LENGKAP => 'Belum Lengkap',
      self::LENGKAP => 'Lengkap',
    };
  }
}
