<?php

namespace App\Constants;

class Kota
{
  public const KAB_ALOR = 'KAB. ALOR';
  public const KAB_BELU = 'KAB. BELU';
  public const KAB_ENDE = 'KAB. ENDE';
  public const KAB_FLORES_TIMUR = 'KAB. FLORES TIMUR';
  public const KAB_KUPANG = 'KAB. KUPANG';
  public const KAB_LEMBATA = 'KAB. LEMBATA';
  public const KAB_MALAKA = 'KAB. MALAKA';
  public const KAB_MANGGARAI = 'KAB. MANGGARAI';
  public const KAB_MANGGARAI_BARAT = 'KAB. MANGGARAI BARAT';
  public const KAB_MANGGARAI_TIMUR = 'KAB. MANGGARAI TIMUR';
  public const KAB_NAGEKEO = 'KAB. NAGEKEO';
  public const KAB_NGADA = 'KAB. NGADA';
  public const KAB_ROTE_NDAO = 'KAB. ROTE NDAO';
  public const KAB_SABU_RAIJUA = 'KAB. SABU RAIJUA';
  public const KAB_SIKKA = 'KAB. SIKKA';
  public const KAB_SUMBA_BARAT = 'KAB. SUMBA BARAT';
  public const KAB_SUMBA_BARAT_DAYA = 'KAB. SUMBA BARAT DAYA';
  public const KAB_SUMBA_TENGAH = 'KAB. SUMBA TENGAH';
  public const KAB_SUMBA_TIMUR = 'KAB. SUMBA TIMUR';
  public const KAB_TIMOR_TENGAH_SELATAN = 'KAB. TIMOR TENGAH SELATAN';
  public const KAB_TIMOR_TENGAH_UTARA = 'KAB. TIMOR TENGAH UTARA';
  public const KOTA_KUPANG = 'KOTA KUPANG';

  /**
   * Get all areas
   * @return array
   */
  public static function all()
  {
    return [
      self::KAB_ALOR,
      self::KAB_BELU,
      self::KAB_ENDE,
      self::KAB_FLORES_TIMUR,
      self::KAB_KUPANG,
      self::KAB_LEMBATA,
      self::KAB_MALAKA,
      self::KAB_MANGGARAI,
      self::KAB_MANGGARAI_BARAT,
      self::KAB_MANGGARAI_TIMUR,
      self::KAB_NAGEKEO,
      self::KAB_NGADA,
      self::KAB_ROTE_NDAO,
      self::KAB_SABU_RAIJUA,
      self::KAB_SIKKA,
      self::KAB_SUMBA_BARAT,
      self::KAB_SUMBA_BARAT_DAYA,
      self::KAB_SUMBA_TENGAH,
      self::KAB_SUMBA_TIMUR,
      self::KAB_TIMOR_TENGAH_SELATAN,
      self::KAB_TIMOR_TENGAH_UTARA,
      self::KOTA_KUPANG
    ];
  }
}
