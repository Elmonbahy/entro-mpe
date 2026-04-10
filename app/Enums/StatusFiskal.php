<?php

namespace App\Enums;

use Illuminate\Support\Carbon;

enum StatusFiskal: string
{
  case UNPAID = 'UNPAID';
  case PARTIAL = 'PARTIAL';
  case COMPLETED = 'COMPLETED';

  public function label(): string
  {
    return match ($this) {
      self::UNPAID => 'Unpaid',
      self::PARTIAL => 'Partial',
      self::COMPLETED => 'Completed',
    };
  }

  public function getDeadline($tanggalFaktur): Carbon
  {
    return Carbon::parse($tanggalFaktur)->addMonthNoOverflow()->day(20)->startOfDay();
  }

  // Perhatikan tanda ? sebelum JenisFiskal
  public function labelDynamic($tanggal, ?JenisFiskal $jenis): string
  {
    // Jika jenis null, ia akan melewati if ini dan lanjut ke pengecekan terlambat
    if ($jenis === JenisFiskal::PERALIHAN) {
      return 'Peralihan ' . $this->label();
    }

    $deadline = $this->getDeadline($tanggal);
    $hariIni = Carbon::now()->startOfDay();

    if ($hariIni->gt($deadline)) {
      return $this->label() . ' (Terlambat)';
    }

    if ($this === self::COMPLETED) {
      return $this->label();
    }

    if ($hariIni->diffInDays($deadline, false) <= 10 && $hariIni <= $deadline) {
      return $this->label() . ' (Warning)';
    }

    return $this->label();
  }

  // Tambahkan ? juga di sini
  public function colorDynamic($tanggal, ?JenisFiskal $jenis): string
  {
    if ($jenis === JenisFiskal::PERALIHAN) {
      return 'text-primary';
    }

    $deadline = $this->getDeadline($tanggal);
    $hariIni = Carbon::now()->startOfDay();

    if ($hariIni->gt($deadline)) {
      return 'text-danger';
    }

    if ($this === self::COMPLETED) {
      return 'text-success';
    }

    $warningLimit = $deadline->copy()->subDays(10);
    if ($hariIni->greaterThanOrEqualTo($warningLimit)) {
      return 'text-warning';
    }

    return 'text-muted';
  }
}
