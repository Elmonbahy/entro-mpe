<?php
namespace App\Constants;

class TipePelanggan
{
  public const RS = 'RUMAH SAKIT';
  public const APTK = 'APOTEK';
  public const DINAS = 'DINAS';
  public const KLINIK = 'KLINIK';
  public const PBF = 'PBF';
  public const PUSKESMAS = 'PUSKESMAS';
  public const CV = 'CV';
  public const DLL = 'DAN LAIN LAIN';



  /**
   * get all tipe pelanggan
   * @return array
   */
  public static function all()
  {
    return [self::RS, self::APTK, self::DINAS, self::KLINIK, self::PBF, self::PUSKESMAS, self::CV, self::DLL];
  }
}
