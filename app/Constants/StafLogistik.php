<?php
namespace App\Constants;

class StafLogistik
{
  public const Saleh_Ahmad = 'Saleh Ahmad';
  public const Daniel = 'Daniel';
  public const Andi = 'Andi';

  /**
   * get all satuan
   * @return array
   */
  public static function all()
  {
    return [self::Saleh_Ahmad, self::Daniel, self::Andi];
  }
}
