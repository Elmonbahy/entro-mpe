<?php
namespace App\Constants;

class Satuan
{
  public const BOX = 'BOX';
  public const PCS = 'PCS';
  public const PACK = 'PACK';
  public const ROLL = 'ROLL';
  public const UNIT = 'UNIT';
  public const LBR = 'LBR';

  public const SATUAN_LIST = [
    '10ML',
    '10ML/VIAL',
    '10TEST/BOX',
    '25TEST/BOX',
    '30TEST/BOX',
    '40TEST/BOX',
    '5ML/VIAL',
    '50TEST/BOX',
    'BAG',
    'BAG/100',
    'BARIS',
    'BOX',
    'BOX/1ROLL',
    'BOX/100',
    'BOX/100PCS',
    'BOX/100\'s',
    'BOX/10\'s',
    'BOX/12\'s',
    'BOX/200',
    'BOX/20\'S',
    'BOX/24\'S',
    'BOX/30',
    'BOX/50',
    'BOX/50\'S',
    'BOX/6\'S',
    'BTL',
    'BUAH',
    'BUNGKUS',
    'DUS',
    'FLS',
    'GLN',
    'JRG',
    'KG',
    'KIT',
    'LBR',
    'LUSIN',
    'METER',
    'PACK',
    'PACK/50',
    'PAK',
    'PAK/10',
    'PAK/100',
    'PAK/50ROLL',
    'PAK/500',
    'PCS',
    'POT',
    'POUCH',
    'PSG',
    'RACK',
    'ROLL',
    'SASET',
    'SET',
    'SHEET',
    'STRIP',
    'SYRINGE',
    'TABUNG',
    'TUBE',
    'UNIT',
    'VIAL',
    '250GRAM',
    '20 LITER',
    'Ekor',
    '1L',
    '20KG',
    'KTK',
    'SAK'
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
