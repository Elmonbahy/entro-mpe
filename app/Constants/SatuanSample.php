<?php
namespace App\Constants;

class SatuanSample
{
  public const BOX = 'BOX';
  public const PCS = 'PCS';
  public const PACK = 'PACK';
  public const ROLL = 'ROLL';
  public const UNIT = 'UNIT';
  public const LBR = 'LBR';

  public const SATUAN_LIST = [
    'PCS',
    'UNIT',
    'LBR',
    'ROLL',
    'PACK',
    'BTL',
    'BUAH',
    'TABUNG',
    'VIAL',
    'SASET',
    'STRIP',
    'POT',
    'POUCH',
    'SET',
  ];

  /**
   * Get all satuan
   * @return array
   */
  public static function all()
  {
    return self::SATUAN_LIST;
  }
}
