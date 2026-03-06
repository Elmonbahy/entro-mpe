<?php
namespace App\Constants;

class TipePenjualan
{
  public const Reguler = 'Reguler';
  public const ECatalog = 'E-Catalog';
  public const Tender = 'Tender';

  /**
   * get all satuan
   * @return array
   */
  public static function all()
  {
    return [self::Reguler, self::ECatalog, self::Tender];
  }
}
