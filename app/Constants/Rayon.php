<?php
namespace App\Constants;

class Rayon
{
  public const LUAR = 'LUAR';
  public const DALAM = 'DALAM';

  /**
   * get all rayon
   * @return array
   */
  public static function all()
  {
    return [self::LUAR, self::DALAM];
  }
}
