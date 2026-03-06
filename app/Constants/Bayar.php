<?php
namespace App\Constants;

class Bayar
{
  public const BCA = 'Bank BCA';
  public const MANDIRI = 'Bank Mandiri';
  public const NTT = 'Bank NTT';
  public const TUNAI = 'Tunai';

  // tipe bayar
  public const KONTAN = 'Kontan';
  public const CICIL = 'Cicil';

  public static function getAllMetodeBayar()
  {
    return [self::BCA, self::MANDIRI, self::NTT, self::TUNAI];
  }
  public static function getAllTipeBayar()
  {
    return [self::KONTAN, self::CICIL];
  }

}
