<?php
namespace App\Enums;

enum StatusFaktur: string
{
  case NEW = 'NEW';
  case PROCESS_FAKTUR = 'PROCESS_FAKTUR';
  case PROCESS_GUDANG = 'PROCESS_GUDANG';
  case DONE = 'DONE';

  public function label() : string
  {
    return match ($this) {
      self::NEW => 'Baru',
      self::PROCESS_FAKTUR => 'Proses Faktur',
      self::PROCESS_GUDANG => 'Proses Gudang',
      self::DONE => 'Selesai',
    };
  }

  public function labelPowergridFilter() : string
  {
    return $this->label();
  }
}
