<?php
namespace App\Enums;

enum StatusSample: string
{
  case NEW = 'NEW';
  case PROCESS_SAMPLE = 'PROCESS_SAMPLE';
  case PROCESS_GUDANG = 'PROCESS_GUDANG';
  case DONE = 'DONE';

  public function label(): string
  {
    return match ($this) {
      self::NEW => 'Baru',
      self::PROCESS_SAMPLE => 'Proses Sampel',
      self::PROCESS_GUDANG => 'Proses Gudang',
      self::DONE => 'Selesai',
    };
  }

  public function labelPowergridFilter(): string
  {
    return $this->label();
  }
}
