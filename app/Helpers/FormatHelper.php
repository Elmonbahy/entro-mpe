<?php

if (!function_exists('formatRupiahDinamis')) {
  function formatCurrencyDinamis($angka)
  {
    if ($angka === null || $angka === '') {
      return '-';
    }

    // Buat formatter untuk locale Indonesia
    $fmt = new \NumberFormatter('id_ID', \NumberFormatter::CURRENCY);

    // Cek apakah angka punya desimal
    if (fmod($angka, 1) == 0) {
      $fmt->setAttribute(\NumberFormatter::FRACTION_DIGITS, 0);
    } else {
      $fmt->setAttribute(\NumberFormatter::MIN_FRACTION_DIGITS, 2);
      $fmt->setAttribute(\NumberFormatter::MAX_FRACTION_DIGITS, 4);
    }

    return $fmt->formatCurrency($angka, 'IDR');
  }

  if (!function_exists('formatRupiahTanpaSimbol')) {
    function formatNumberDinamis($angka)
    {
      if ($angka === null || $angka === '') {
        return '-';
      }

      // Formatter locale Indonesia (tanpa simbol Rp)
      $fmt = new \NumberFormatter('id_ID', \NumberFormatter::DECIMAL);

      // Cek apakah angka punya desimal
      if (fmod($angka, 1) == 0) {
        $fmt->setAttribute(\NumberFormatter::FRACTION_DIGITS, 0);
      } else {
        $fmt->setAttribute(\NumberFormatter::MIN_FRACTION_DIGITS, 2);
        $fmt->setAttribute(\NumberFormatter::MAX_FRACTION_DIGITS, 4);
      }

      return $fmt->format($angka);
    }
  }
}
