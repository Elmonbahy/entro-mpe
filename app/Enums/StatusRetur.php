<?php

namespace App\Enums;

enum StatusRetur: string
{
  case PENDING = 'pending';     // Input oleh Fakturis, menunggu Gudang
  case WAITING_TAX = 'waiting_tax'; // Menunggu oleh Pajak (hanya untuk faktur/barang yang sudah diproses fiskal)
  case APPROVED = 'approved';   // Disetujui Gudang (Stok berubah)
  case REJECTED = 'rejected';   // Ditolak Gudang (Stok tidak berubah)

  public function label(): string
  {
    return match ($this) {
      self::PENDING => 'Menunggu Verifikasi Gudang',
      self::WAITING_TAX => 'Menunggu Verifikasi Pajak',
      self::APPROVED => 'Disetujui',
      self::REJECTED => 'Ditolak',
    };
  }

  public function color(): string
  {
    return match ($this) {
      self::PENDING => 'warning',
      self::WAITING_TAX => 'info',
      self::APPROVED => 'success',
      self::REJECTED => 'danger',
    };
  }
}