<?php

namespace App\Constants;

class Area
{
  public const ALOR = 'ALOR';
  public const FLORES = 'FLORES';
  public const KAB_KUPANG = 'KAB. KUPANG';
  public const KOTA_KUPANG = 'KOTA KUPANG';
  public const SUMBA = 'SUMBA';
  public const ROTE = 'ROTE';
  public const SABU = 'SABU';
  public const TIMOR = 'TIMOR';

  /**
   * Get all areas
   * @return array
   */
  public static function all()
  {
    return [
      self::ALOR,
      self::FLORES,
      self::KAB_KUPANG,
      self::KOTA_KUPANG,
      self::SUMBA,
      self::ROTE,
      self::SABU,
      self::TIMOR,
    ];
  }
}
