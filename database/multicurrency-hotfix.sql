-- =============================================================================
-- Multicurrency hotfix: decode HTML-entity currency symbols stored in the DB
--
-- The first version of CreateNewUser / CurrencyChangeController stored the
-- currency symbol as a raw HTML entity string (e.g. '&#76;' for SZL). Display
-- code uses Blade's escaping {{ }}, so users saw the literal '&#76;' instead
-- of 'L'. This patch maps every affected row to the actual unicode glyph.
--
-- Safe to run multiple times: each UPDATE is keyed on the exact entity string
-- and only touches users whose currency still matches that entity.
--
-- Run from MySQL after importing multicurrency.sql:
--     mysql -u USER -p DATABASE < database/multicurrency-hotfix.sql
-- =============================================================================

START TRANSACTION;

-- Map: s_currency code -> actual unicode glyph for the matching symbol entity.
-- Generated from config/currencies.php.
UPDATE `users` SET `currency` = 'د.إ'   WHERE `s_currency` = 'AED' AND `currency` = '&#1583;.&#1573;';
UPDATE `users` SET `currency` = 'Af'    WHERE `s_currency` = 'AFN' AND `currency` = '&#65;&#102;';
UPDATE `users` SET `currency` = 'Lek'   WHERE `s_currency` = 'ALL' AND `currency` = '&#76;&#101;&#107;';
UPDATE `users` SET `currency` = 'ƒ'    WHERE `s_currency` = 'ANG' AND `currency` = '&#402;';
UPDATE `users` SET `currency` = 'Kz'    WHERE `s_currency` = 'AOA' AND `currency` = '&#75;&#122;';
UPDATE `users` SET `currency` = '$'     WHERE `s_currency` IN ('ARS','AUD','BBD','BMD','BND','CAD','CLP','COP','FJD','GYD','HKD','KYD','LRD','MXN','NAD','NZD','SBD','SGD','SRD','SVC','XCD') AND `currency` = '&#36;';
UPDATE `users` SET `currency` = 'ƒ'    WHERE `s_currency` = 'AWG' AND `currency` = '&#402;';
UPDATE `users` SET `currency` = 'ман'  WHERE `s_currency` = 'AZN' AND `currency` = '&#1084;&#1072;&#1085;';
UPDATE `users` SET `currency` = 'KM'    WHERE `s_currency` = 'BAM' AND `currency` = '&#75;&#77;';
UPDATE `users` SET `currency` = '৳'    WHERE `s_currency` = 'BDT' AND `currency` = '&#2547;';
UPDATE `users` SET `currency` = 'лв'   WHERE `s_currency` IN ('BGN','KGS','KZT','UZS') AND `currency` = '&#1083;&#1074;';
UPDATE `users` SET `currency` = '.د.ب' WHERE `s_currency` = 'BHD' AND `currency` = '.&#1583;.&#1576;';
UPDATE `users` SET `currency` = 'FBu'   WHERE `s_currency` = 'BIF' AND `currency` = '&#70;&#66;&#117;';
UPDATE `users` SET `currency` = '$b'    WHERE `s_currency` = 'BOB' AND `currency` = '&#36;&#98;';
UPDATE `users` SET `currency` = 'R$'    WHERE `s_currency` = 'BRL' AND `currency` = '&#82;&#36;';
UPDATE `users` SET `currency` = 'Nu.'   WHERE `s_currency` = 'BTN' AND `currency` = '&#78;&#117;&#46;';
UPDATE `users` SET `currency` = 'P'     WHERE `s_currency` = 'BWP' AND `currency` = '&#80;';
UPDATE `users` SET `currency` = 'p.'    WHERE `s_currency` = 'BYR' AND `currency` = '&#112;&#46;';
UPDATE `users` SET `currency` = 'BZ$'   WHERE `s_currency` = 'BZD' AND `currency` = '&#66;&#90;&#36;';
UPDATE `users` SET `currency` = 'FC'    WHERE `s_currency` = 'CDF' AND `currency` = '&#70;&#67;';
UPDATE `users` SET `currency` = 'CHF'   WHERE `s_currency` = 'CHF' AND `currency` = '&#67;&#72;&#70;';
UPDATE `users` SET `currency` = '¥'     WHERE `s_currency` IN ('CNY','JPY') AND `currency` = '&#165;';
UPDATE `users` SET `currency` = '₡'    WHERE `s_currency` = 'CRC' AND `currency` = '&#8353;';
UPDATE `users` SET `currency` = '₧'    WHERE `s_currency` = 'CUP' AND `currency` = '&#8396;';
UPDATE `users` SET `currency` = 'Kč'   WHERE `s_currency` = 'CZK' AND `currency` = '&#75;&#269;';
UPDATE `users` SET `currency` = 'Fdj'   WHERE `s_currency` = 'DJF' AND `currency` = '&#70;&#100;&#106;';
UPDATE `users` SET `currency` = 'kr'    WHERE `s_currency` IN ('DKK','ISK','NOK','SEK') AND `currency` = '&#107;&#114;';
UPDATE `users` SET `currency` = 'RD$'   WHERE `s_currency` = 'DOP' AND `currency` = '&#82;&#68;&#36;';
UPDATE `users` SET `currency` = 'دج'   WHERE `s_currency` = 'DZD' AND `currency` = '&#1583;&#1580;';
UPDATE `users` SET `currency` = '£'     WHERE `s_currency` IN ('EGP','FKP','GBP','GIP','JEP','LBP','SDG','SHP','SYP') AND `currency` = '&#163;';
UPDATE `users` SET `currency` = 'Br'    WHERE `s_currency` = 'ETB' AND `currency` = '&#66;&#114;';
UPDATE `users` SET `currency` = '€'     WHERE `s_currency` = 'EUR' AND `currency` = '&#8364;';
UPDATE `users` SET `currency` = 'ლ'    WHERE `s_currency` = 'GEL' AND `currency` = '&#4314;';
UPDATE `users` SET `currency` = '¢'     WHERE `s_currency` = 'GHS' AND `currency` = '&#162;';
UPDATE `users` SET `currency` = 'D'     WHERE `s_currency` = 'GMD' AND `currency` = '&#68;';
UPDATE `users` SET `currency` = 'FG'    WHERE `s_currency` = 'GNF' AND `currency` = '&#70;&#71;';
UPDATE `users` SET `currency` = 'Q'     WHERE `s_currency` = 'GTQ' AND `currency` = '&#81;';
UPDATE `users` SET `currency` = 'L'     WHERE `s_currency` IN ('HNL','LSL','MDL','SZL') AND `currency` = '&#76;';
UPDATE `users` SET `currency` = 'kn'    WHERE `s_currency` = 'HRK' AND `currency` = '&#107;&#110;';
UPDATE `users` SET `currency` = 'G'     WHERE `s_currency` = 'HTG' AND `currency` = '&#71;';
UPDATE `users` SET `currency` = 'Ft'    WHERE `s_currency` = 'HUF' AND `currency` = '&#70;&#116;';
UPDATE `users` SET `currency` = 'Rp'    WHERE `s_currency` = 'IDR' AND `currency` = '&#82;&#112;';
UPDATE `users` SET `currency` = '₪'    WHERE `s_currency` = 'ILS' AND `currency` = '&#8362;';
UPDATE `users` SET `currency` = '₹'    WHERE `s_currency` = 'INR' AND `currency` = '&#8377;';
UPDATE `users` SET `currency` = 'ع.د'  WHERE `s_currency` = 'IQD' AND `currency` = '&#1593;.&#1583;';
UPDATE `users` SET `currency` = '﷼'    WHERE `s_currency` IN ('IRR','OMR','QAR','SAR','YER') AND `currency` = '&#65020;';
UPDATE `users` SET `currency` = 'J$'    WHERE `s_currency` = 'JMD' AND `currency` = '&#74;&#36;';
UPDATE `users` SET `currency` = 'JD'    WHERE `s_currency` = 'JOD' AND `currency` = '&#74;&#68;';
UPDATE `users` SET `currency` = 'KSh'   WHERE `s_currency` = 'KES' AND `currency` = '&#75;&#83;&#104;';
UPDATE `users` SET `currency` = '៛'    WHERE `s_currency` = 'KHR' AND `currency` = '&#6107;';
UPDATE `users` SET `currency` = 'CF'    WHERE `s_currency` = 'KMF' AND `currency` = '&#67;&#70;';
UPDATE `users` SET `currency` = '₩'    WHERE `s_currency` IN ('KPW','KRW') AND `currency` = '&#8361;';
UPDATE `users` SET `currency` = 'د.ك'  WHERE `s_currency` = 'KWD' AND `currency` = '&#1583;.&#1603;';
UPDATE `users` SET `currency` = '₭'    WHERE `s_currency` = 'LAK' AND `currency` = '&#8365;';
UPDATE `users` SET `currency` = '₨'    WHERE `s_currency` IN ('LKR','MUR','NPR','PKR','SCR') AND `currency` = '&#8360;';
UPDATE `users` SET `currency` = 'Lt'    WHERE `s_currency` = 'LTL' AND `currency` = '&#76;&#116;';
UPDATE `users` SET `currency` = 'Ls'    WHERE `s_currency` = 'LVL' AND `currency` = '&#76;&#115;';
UPDATE `users` SET `currency` = 'ل.د'  WHERE `s_currency` = 'LYD' AND `currency` = '&#1604;.&#1583;';
UPDATE `users` SET `currency` = 'د.م.' WHERE `s_currency` = 'MAD' AND `currency` = '&#1583;.&#1605;.';
UPDATE `users` SET `currency` = 'Ar'    WHERE `s_currency` = 'MGA' AND `currency` = '&#65;&#114;';
UPDATE `users` SET `currency` = 'ден'  WHERE `s_currency` = 'MKD' AND `currency` = '&#1076;&#1077;&#1085;';
UPDATE `users` SET `currency` = 'K'     WHERE `s_currency` IN ('MMK','PGK') AND `currency` = '&#75;';
UPDATE `users` SET `currency` = '₮'    WHERE `s_currency` = 'MNT' AND `currency` = '&#8366;';
UPDATE `users` SET `currency` = 'MOP$'  WHERE `s_currency` = 'MOP' AND `currency` = '&#77;&#79;&#80;&#36;';
UPDATE `users` SET `currency` = 'UM'    WHERE `s_currency` = 'MRO' AND `currency` = '&#85;&#77;';
UPDATE `users` SET `currency` = '.ރ'   WHERE `s_currency` = 'MVR' AND `currency` = '.&#1923;';
UPDATE `users` SET `currency` = 'MK'    WHERE `s_currency` = 'MWK' AND `currency` = '&#77;&#75;';
UPDATE `users` SET `currency` = 'RM'    WHERE `s_currency` = 'MYR' AND `currency` = '&#82;&#77;';
UPDATE `users` SET `currency` = 'MT'    WHERE `s_currency` = 'MZN' AND `currency` = '&#77;&#84;';
UPDATE `users` SET `currency` = '₦'    WHERE `s_currency` = 'NGN' AND `currency` = '&#8358;';
UPDATE `users` SET `currency` = 'C$'    WHERE `s_currency` = 'NIO' AND `currency` = '&#67;&#36;';
UPDATE `users` SET `currency` = 'B/.'   WHERE `s_currency` = 'PAB' AND `currency` = '&#66;&#47;&#46;';
UPDATE `users` SET `currency` = 'S/.'   WHERE `s_currency` = 'PEN' AND `currency` = '&#83;&#47;&#46;';
UPDATE `users` SET `currency` = '₱'    WHERE `s_currency` = 'PHP' AND `currency` = '&#8369;';
UPDATE `users` SET `currency` = 'zł'   WHERE `s_currency` = 'PLN' AND `currency` = '&#122;&#322;';
UPDATE `users` SET `currency` = 'Gs'    WHERE `s_currency` = 'PYG' AND `currency` = '&#71;&#115;';
UPDATE `users` SET `currency` = 'lei'   WHERE `s_currency` = 'RON' AND `currency` = '&#108;&#101;&#105;';
UPDATE `users` SET `currency` = 'Дин.' WHERE `s_currency` = 'RSD' AND `currency` = '&#1044;&#1080;&#1085;&#46;';
UPDATE `users` SET `currency` = 'руб'  WHERE `s_currency` = 'RUB' AND `currency` = '&#1088;&#1091;&#1073;';
UPDATE `users` SET `currency` = 'ر.س'  WHERE `s_currency` = 'RWF' AND `currency` = '&#1585;.&#1587;';
UPDATE `users` SET `currency` = 'Le'    WHERE `s_currency` = 'SLL' AND `currency` = '&#76;&#101;';
UPDATE `users` SET `currency` = 'S'     WHERE `s_currency` = 'SOS' AND `currency` = '&#83;';
UPDATE `users` SET `currency` = 'Db'    WHERE `s_currency` = 'STD' AND `currency` = '&#68;&#98;';
UPDATE `users` SET `currency` = '฿'    WHERE `s_currency` = 'THB' AND `currency` = '&#3647;';
UPDATE `users` SET `currency` = 'TJS'   WHERE `s_currency` = 'TJS' AND `currency` = '&#84;&#74;&#83;';
UPDATE `users` SET `currency` = 'm'     WHERE `s_currency` = 'TMT' AND `currency` = '&#109;';
UPDATE `users` SET `currency` = 'د.ت'  WHERE `s_currency` = 'TND' AND `currency` = '&#1583;&#1578;';
UPDATE `users` SET `currency` = 'T$'    WHERE `s_currency` = 'TOP' AND `currency` = '&#84;&#36;';
UPDATE `users` SET `currency` = '₤'    WHERE `s_currency` = 'TRY' AND `currency` = '&#8356;';
UPDATE `users` SET `currency` = 'NT$'   WHERE `s_currency` = 'TWD' AND `currency` = '&#78;&#84;&#36;';
UPDATE `users` SET `currency` = '₴'    WHERE `s_currency` = 'UAH' AND `currency` = '&#8372;';
UPDATE `users` SET `currency` = 'USh'   WHERE `s_currency` = 'UGX' AND `currency` = '&#85;&#83;&#104;';
UPDATE `users` SET `currency` = '$U'    WHERE `s_currency` = 'UYU' AND `currency` = '&#36;&#85;';
UPDATE `users` SET `currency` = 'Bs'    WHERE `s_currency` = 'VEF' AND `currency` = '&#66;&#115;';
UPDATE `users` SET `currency` = '₫'    WHERE `s_currency` = 'VND' AND `currency` = '&#8363;';
UPDATE `users` SET `currency` = 'VT'    WHERE `s_currency` = 'VUV' AND `currency` = '&#86;&#84;';
UPDATE `users` SET `currency` = 'WS$'   WHERE `s_currency` = 'WST' AND `currency` = '&#87;&#83;&#36;';
UPDATE `users` SET `currency` = 'FCFA'  WHERE `s_currency` = 'XAF' AND `currency` = '&#70;&#67;&#70;&#65;';
UPDATE `users` SET `currency` = 'F'     WHERE `s_currency` = 'XPF' AND `currency` = '&#70;';
UPDATE `users` SET `currency` = 'R'     WHERE `s_currency` = 'ZAR' AND `currency` = '&#82;';
UPDATE `users` SET `currency` = 'ZK'    WHERE `s_currency` = 'ZMK' AND `currency` = '&#90;&#75;';
UPDATE `users` SET `currency` = 'Z$'    WHERE `s_currency` = 'ZWL' AND `currency` = '&#90;&#36;';

-- Fix the `currency` column when `requested_currency_symbol` was stored as an
-- entity too (same logic). Only touches non-null pending values.
UPDATE `users` SET `requested_currency_symbol` = 'L'
    WHERE `requested_currency` IN ('HNL','LSL','MDL','SZL') AND `requested_currency_symbol` = '&#76;';

COMMIT;
