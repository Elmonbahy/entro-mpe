<?php

namespace App\Enums;

enum StatusRetur: string
{
  case PENDING = 'pending';     // Input oleh Fakturis, menunggu Gudang
  case APPROVED = 'approved';   // Disetujui Gudang (Stok berubah)
  case REJECTED = 'rejected';   // Ditolak Gudang (Stok tidak berubah)

  public function label(): string
  {
    return match ($this) {
      self::PENDING => 'Menunggu Verifikasi',
      self::APPROVED => 'Disetujui',
      self::REJECTED => 'Ditolak',
    };
  }

  public function color(): string
  {
    return match ($this) {
      self::PENDING => 'warning',
      self::APPROVED => 'success',
      self::REJECTED => 'danger',
    };
  }
}