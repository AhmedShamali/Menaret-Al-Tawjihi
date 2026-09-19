<?php

namespace App\Support;

class Tafqeet
{
    private static array $ones = [
        0 => '',
        1 => 'واحد',
        2 => 'اثنان',
        3 => 'ثلاثة',
        4 => 'أربعة',
        5 => 'خمسة',
        6 => 'ستة',
        7 => 'سبعة',
        8 => 'ثمانية',
        9 => 'تسعة',
        10 => 'عشرة',
        11 => 'أحد عشر',
        12 => 'اثنا عشر',
        13 => 'ثلاثة عشر',
        14 => 'أربعة عشر',
        15 => 'خمسة عشر',
        16 => 'ستة عشر',
        17 => 'سبعة عشر',
        18 => 'ثمانية عشر',
        19 => 'تسعة عشر',
    ];

    private static array $tens = [
        2 => 'عشرون',
        3 => 'ثلاثون',
        4 => 'أربعون',
        5 => 'خمسون',
        6 => 'ستون',
        7 => 'سبعون',
        8 => 'ثمانون',
        9 => 'تسعون',
    ];

    private static array $hundreds = [
        0 => '',
        1 => 'مائة',
        2 => 'مائتان',
        3 => 'ثلاثمائة',
        4 => 'أربعمائة',
        5 => 'خمسمائة',
        6 => 'ستمائة',
        7 => 'سبعمائة',
        8 => 'ثمانمائة',
        9 => 'تسعمائة',
    ];

    /**
     * تحويل المبلغ إلى كلمات باللغة العربية مع عملة الشيكل الفلسطيني
     */
    public static function inArabic(float|int|string $amount, string $currency = 'شيكل', string $subCurrency = 'أغورة'): string
    {
        $num = (float) $amount;
        if ($num <= 0) {
            return 'صفر ' . $currency;
        }

        $integerPart = (int) floor($num);
        $decimalPart = (int) round(($num - $integerPart) * 100);

        $words = self::convertNumber($integerPart);
        $result = 'فقط ' . $words . ' ' . self::getCurrencyFormat($integerPart, $currency);

        if ($decimalPart > 0) {
            $decimalWords = self::convertNumber($decimalPart);
            $result .= ' و ' . $decimalWords . ' ' . $subCurrency;
        }

        $result .= ' لا غير';

        return $result;
    }

    private static function getCurrencyFormat(int $n, string $currency): string
    {
        if ($currency === 'شيكل') {
            if ($n >= 3 && $n <= 10) {
                return 'شواكل';
            }
            if ($n >= 11) {
                return 'شيكلاً';
            }
            return 'شيكل';
        }
        return $currency;
    }

    private static function convertNumber(int $num): string
    {
        if ($num === 0) {
            return '';
        }

        if ($num < 20) {
            return self::$ones[$num];
        }

        if ($num < 100) {
            $ten = (int) floor($num / 10);
            $rem = $num % 10;
            if ($rem === 0) {
                return self::$tens[$ten];
            }
            return self::$ones[$rem] . ' و ' . self::$tens[$ten];
        }

        if ($num < 1000) {
            $hundred = (int) floor($num / 100);
            $rem = $num % 100;
            if ($rem === 0) {
                return self::$hundreds[$hundred];
            }
            return self::$hundreds[$hundred] . ' و ' . self::convertNumber($rem);
        }

        if ($num < 1000000) {
            $thousands = (int) floor($num / 1000);
            $rem = $num % 1000;

            $thousandsText = '';
            if ($thousands === 1) {
                $thousandsText = 'ألف';
            } elseif ($thousands === 2) {
                $thousandsText = 'ألفان';
            } elseif ($thousands >= 3 && $thousands <= 10) {
                $thousandsText = self::convertNumber($thousands) . ' آلاف';
            } else {
                $thousandsText = self::convertNumber($thousands) . ' ألفاً';
            }

            if ($rem === 0) {
                return $thousandsText;
            }
            return $thousandsText . ' و ' . self::convertNumber($rem);
        }

        return (string) $num;
    }
}
