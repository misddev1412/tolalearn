<?php

function getTemplate()
{
    /*$template = cache()->remember('view.template', 7 * 24 * 60 * 60, function () {
        return \App\Models\ViewTemplate::where('status', true)->first();
    });*/
    if (!empty($template) and $template->count() > 0) {
        return 'web.' . $template->folder;
    }
    return 'web.default';
}

function formatSizeUnits($bytes)
{
    if ($bytes >= 1073741824) {
        $bytes = number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        $bytes = number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        $bytes = number_format($bytes / 1024, 2) . ' KB';
    } elseif ($bytes > 1) {
        $bytes = $bytes . ' bytes';
    } elseif ($bytes == 1) {
        $bytes = $bytes . ' byte';
    } else {
        $bytes = '0 bytes';
    }
    return $bytes;
}

function dateTimeFormat($timestamp, $format = 'H:i')
{
    return (new DateTime())->setTimestamp($timestamp)->format($format);
}

function diffTimestampDay($firstTime, $lastTime)
{
    return ($firstTime - $lastTime) / (24 * 60 * 60);
}

function convertMinutesToHourAndMinute($minutes)
{
    return intdiv($minutes, 60) . ':' . (str_pad($minutes % 60, 2, 0, STR_PAD_LEFT));
}

function x_week_range()
{
    $start = strtotime(date('Y-m-d', strtotime("last Saturday")));
    return array(
        $start,
        strtotime(date('Y-m-d', strtotime('next Friday', $start)))
    );
}

function getTimeByDay($title)
{
    $start = date('Y-m-d', strtotime("last Saturday"));
    $time = 0;
    switch ($title) {
        case "saturday":
            $time = strtotime(date('Y-m-d', strtotime($start)));
            break;
        case "sunday":
            $time = strtotime(date('Y-m-d', strtotime($start . "+1 days")));
            break;
        case "monday":
            $time = strtotime(date('Y-m-d', strtotime($start . "+2 days")));
            break;
        case "tuesday":
            $time = strtotime(date('Y-m-d', strtotime($start . "+3 days")));
            break;
        case "wednesday":
            $time = strtotime(date('Y-m-d', strtotime($start . "+4 days")));
            break;
        case "thursday":
            $time = strtotime(date('Y-m-d', strtotime($start . "+5 days")));
            break;
        case "friday":
            $time = strtotime(date('Y-m-d', strtotime($start . "+6 days")));
            break;
    }
    return $time;
}

function convertDayToNumber($times)
{
    $numbers = [
        'sunday' => 1,
        'monday' => 2,
        'tuesday' => 3,
        'wednesday' => 4,
        'thursday' => 5,
        'friday' => 6,
        'saturday' => 7
    ];

    $numberDay = [];

    foreach ($times as $day => $time) {
        $numberDay[] = $numbers[$day];
    }

    return $numberDay;
}

function getBindedSQL($query)
{
    $fullQuery = $query->toSql();
    $replaces = $query->getBindings();
    foreach ($replaces as $replace) {
        $fullQuery = Str::replaceFirst('?', $replace, $fullQuery);
    }

    return $fullQuery;
}


function getLanguages($lang = null)
{
    $languages = [
        "AA" => 'Afar',
        "AF" => 'Afrikanns',
        "SQ" => 'Albanian',
        "AM" => 'Amharic',
        "AR" => 'Arabic',
        "HY" => 'Armenian',
        "AY" => 'Aymara',
        "AZ" => 'Azerbaijani',
        "EU" => 'Basque',
        "DZ" => 'Bhutani',
        "BH" => 'Bihari',
        "BI" => 'Bislama',
        "BR" => 'Breton',
        "BG" => 'Bulgarian',
        "MY" => 'Burmese',
        "BE" => 'Byelorussian',
        "KM" => 'Cambodian',
        "CA" => 'Catalan',
        "ZH" => 'Chinese',
        "HR" => 'Croation',
        "CS" => 'Czech',
        "DA" => 'Danish',
        "NL" => 'Dutch',
        "EN" => 'English',
        "ET" => 'Estonian',
        "FO" => 'Faeroese',
        "FJ" => 'Fiji',
        "FI" => 'Finnish',
        "FR" => 'French',
        "KA" => 'Georgian',
        "DE" => 'German',
        "EL" => 'Greek',
        "KL" => 'Greenlandic',
        "GN" => 'Guarani',
        "HI" => 'Hindi',
        "HU" => 'Hungarian',
        "IS" => 'Icelandic',
        "ID" => 'Indonesian',
        "IT" => 'Italian',
        "JA" => 'Japanese',
        "KK" => 'Kazakh',
        "RW" => 'Kinyarwanda',
        "KY" => 'Kirghiz',
        "KO" => 'Korean',
        "KU" => 'Kurdish',
        "LO" => 'Laothian',
        "LA" => 'Latin',
        "LV" => 'Latvian',
        "LT" => 'Lithuanian',
        "MK" => 'Macedonian',
        "MG" => 'Malagasy',
        "MS" => 'Malay',
        "MT" => 'Maltese',
        "MI" => 'Maori',
        "MN" => 'Mongolian',
        "NA" => 'Nauru',
        "NE" => 'Nepali',
        "NO" => 'Norwegian',
        "OM" => 'Oromo',
        "PS" => 'Pashto',
        "FA" => 'Persian',
        "PL" => 'Polish',
        "PT" => 'Portuguese',
        "QU" => 'Quechua',
        "RM" => 'Rhaeto',
        "RO" => 'Romanian',
        "RU" => 'Russian',
        "SM" => 'Samoan',
        "SG" => 'Sangro',
        "SR" => 'Serbian',
        "TN" => 'Setswana',
        "SN" => 'Shona',
        "SI" => 'Singhalese',
        "SS" => 'Siswati',
        "SK" => 'Slovak',
        "SL" => 'Slovenian',
        "SO" => 'Somali',
        "ES" => 'Spanish',
        "SV" => 'Swedish',
        "TL" => 'Tagalog',
        "TG" => 'Tajik',
        "TA" => 'Tamil',
        "TH" => 'Thai',
        "TI" => 'Tigrinya',
        "TR" => 'Turkish',
        "TK" => 'Turkmen',
        "TW" => 'Twi',
        "UK" => 'Ukranian',
        "UR" => 'Urdu',
        "UZ" => 'Uzbek',
        "VI" => 'Vietnamese',
        "XH" => 'Xhosa',
    ];

    if (!empty($lang) and is_array($lang)) {
        return array_flip(array_intersect(array_flip($languages), $lang));
    } elseif (!empty($lang)) {
        return $languages[$lang];
    }

    return $languages;
}

function localeToCountryCode($code, $revers = false)
{
    $languages = [
        "AA" => 'DJ', // language code => country code
        "AF" => 'ZA',
        "SQ" => 'AL',
        "AM" => 'ET',
        "AR" => 'IQ',
        "HY" => 'AM',
        "AY" => 'BO',
        "AZ" => 'AZ',
        "EU" => 'ES',
        "BN" => 'BD',
        "DZ" => 'BT',
        "BI" => 'VU',
        "BG" => 'BG',
        "MY" => 'MM',
        "BE" => 'BY',
        "KM" => 'KH',
        "CA" => 'ES',
        "ZH" => 'CN',
        "HR" => 'HR',
        "CS" => 'CZ',
        "DA" => 'DK',
        "NL" => 'NL',
        "EN" => 'US',
        "ET" => 'EE',
        "FO" => 'FO',
        "FJ" => 'FJ',
        "FI" => 'FI',
        "FR" => 'FR',
        "KA" => 'GE',
        "DE" => 'DE',
        "EL" => 'GR',
        "KL" => 'GL',
        "GN" => 'GN',
        "HI" => 'IN',
        "HU" => 'HU',
        "IS" => 'IS',
        "ID" => 'ID',
        "IT" => 'IT',
        "JA" => 'JP',
        "KK" => 'KZ',
        "RW" => 'RW',
        "KY" => 'KG',
        "KO" => 'KR',
        "LO" => 'LA',
        "LA" => 'RS',
        "LV" => 'LV',
        "LT" => 'LT',
        "MK" => 'MK',
        "MG" => 'MG',
        "MS" => 'MS',
        "MT" => 'MT',
        "MI" => 'NZ',
        "MN" => 'MN',
        "NA" => 'NR',
        "NE" => 'NP',
        "NO" => 'NO',
        "OM" => 'ET',
        "PS" => 'AF',
        "FA" => 'IR',
        "PL" => 'PL',
        "PT" => 'PT',
        "QU" => 'BO',
        "RM" => 'CH',
        "RO" => 'RO',
        "RU" => 'RU',
        "SM" => 'WS',
        "SG" => 'CG',
        "SR" => 'SR',
        "TN" => 'BW',
        "SN" => 'ZW',
        "SI" => 'LK',
        "SS" => 'SZ',
        "SK" => 'SK',
        "SL" => 'SI',
        "SO" => 'SO',
        "ES" => 'ES',
        "SV" => 'SE',
        "TL" => 'PH',
        "TG" => 'TJ',
        "TA" => 'LK',
        "TH" => 'TH',
        "TI" => 'ER',
        "TR" => 'TR',
        "TK" => 'TM',
        "TW" => 'TW',
        "UK" => 'UA',
        "UR" => 'PK',
        "UZ" => 'UZ',
        "VI" => 'VN',
        "XH" => 'ZA',
    ];

    if ($revers) {
        $languages = array_flip($languages);
        return !empty($languages[$code]) ? $languages[$code] : '';
    }

    return !empty($languages[$code]) ? $languages[$code] : '';
}

function getMoneyUnits($unit = null)
{
    $units = [
        "USD" => 'United States Dollar',
        "EUR" => 'Euro Member Countries',
        "AUD" => 'Australia Dollar',
        "AED" => 'United Arab Emirates dirham',
        "KAD" => 'KAD',
        "JPY" => 'Japan Yen',
        "CNY" => 'China Yuan Renminbi',
        "SAR" => 'Saudi Arabia Riyal',
        "KRW" => 'Korea (South) Won',
        "INR" => 'India Rupee',
        "RUB" => 'Russia Ruble',
        "Lek" => 'Albania Lek',
        "AFN" => 'Afghanistan Afghani',
        "ARS" => 'Argentina Peso',
        "AWG" => 'Aruba Guilder',
        "AZN" => 'Azerbaijan Manat',
        "BSD" => 'Bahamas Dollar',
        "BBD" => 'Barbados Dollar',
        "BYN" => 'Belarus Ruble',
        "BZD" => 'Belize Dollar',
        "BMD" => 'Bermuda Dollar',
        "BOB" => 'Bolivia Bolíviano',
        "BAM" => 'Bosnia and Herzegovina Convertible Mark',
        "BWP" => 'Botswana Pula',
        "BGN" => 'Bulgaria Lev',
        "BRL" => 'Brazil Real',
        "BND" => 'Brunei Darussalam Dollar',
        "KHR" => 'Cambodia Riel',
        "CAD" => 'Canada Dollar',
        "KYD" => 'Cayman Islands Dollar',
        "CLP" => 'Chile Peso',
        "COP" => 'Colombia Peso',
        "CRC" => 'Costa Rica Colon',
        "HRK" => 'Croatia Kuna',
        "CUP" => 'Cuba Peso',
        "CZK" => 'Czech Republic Koruna',
        "DKK" => 'Denmark Krone',
        "DOP" => 'Dominican Republic Peso',
        "XCD" => 'East Caribbean Dollar',
        "EGP" => 'Egypt Pound',
        "GTQ" => 'Guatemala Quetzal',
        "HKD" => 'Hong Kong Dollar',
        "HUF" => 'Hungary Forint',
        "IDR" => 'Indonesia Rupiah',
        "IRR" => 'Iran Rial',
        "ILS" => 'Israel Shekel',
        "LBP" => 'Lebanon Pound',
        "MYR" => 'Malaysia Ringgit',
        "NGN" => 'Nigeria Naira',
        "NOK" => 'Norway Krone',
        "OMR" => 'Oman Rial',
        "PKR" => 'Pakistan Rupee',
        "PHP" => 'Philippines Peso',
        "PLN" => 'Poland Zloty',
        "RON" => 'Romania Leu',
        "ZAR" => 'South Africa Rand',
        "LKR" => 'Sri Lanka Rupee',
        "SEK" => 'Sweden Krona',
        "CHF" => 'Switzerland Franc',
        "THB" => 'Thailand Baht',
        "TRY" => 'Turkey Lira',
        "UAH" => 'Ukraine Hryvnia',
        "GBP" => 'United Kingdom Pound',
        "TWD" => 'Taiwan New Dollar',
        "VND" => 'Viet Nam Dong',
        "UZS" => 'Uzbekistan Som',
    ];

    if (!empty($unit)) {
        return $units[$unit];
    }

    return $units;
}

function currenciesLists($sing = null)
{
    $lists = [
        "USD" => 'United States Dollar',
        "EUR" => 'Euro Member Countries',
        "AUD" => 'Australia Dollar',
        "AED" => 'United Arab Emirates dirham',
        "KAD" => 'KAD',
        "JPY" => 'Japan Yen',
        "CNY" => 'China Yuan Renminbi',
        "SAR" => 'Saudi Arabia Riyal',
        "KRW" => 'Korea (South) Won',
        "INR" => 'India Rupee',
        "RUB" => 'Russia Ruble',
        "Lek" => 'Albania Lek',
        "AFN" => 'Afghanistan Afghani',
        "ARS" => 'Argentina Peso',
        "AWG" => 'Aruba Guilder',
        "AZN" => 'Azerbaijan Manat',
        "BSD" => 'Bahamas Dollar',
        "BBD" => 'Barbados Dollar',
        "BYN" => 'Belarus Ruble',
        "BZD" => 'Belize Dollar',
        "BMD" => 'Bermuda Dollar',
        "BOB" => 'Bolivia Bolíviano',
        "BAM" => 'Bosnia and Herzegovina Convertible Mark',
        "BWP" => 'Botswana Pula',
        "BGN" => 'Bulgaria Lev',
        "BRL" => 'Brazil Real',
        "BND" => 'Brunei Darussalam Dollar',
        "KHR" => 'Cambodia Riel',
        "CAD" => 'Canada Dollar',
        "KYD" => 'Cayman Islands Dollar',
        "CLP" => 'Chile Peso',
        "COP" => 'Colombia Peso',
        "CRC" => 'Costa Rica Colon',
        "HRK" => 'Croatia Kuna',
        "CUP" => 'Cuba Peso',
        "CZK" => 'Czech Republic Koruna',
        "DKK" => 'Denmark Krone',
        "DOP" => 'Dominican Republic Peso',
        "XCD" => 'East Caribbean Dollar',
        "EGP" => 'Egypt Pound',
        "GTQ" => 'Guatemala Quetzal',
        "HKD" => 'Hong Kong Dollar',
        "HUF" => 'Hungary Forint',
        "IDR" => 'Indonesia Rupiah',
        "IRR" => 'Iran Rial',
        "ILS" => 'Israel Shekel',
        "LBP" => 'Lebanon Pound',
        "MYR" => 'Malaysia Ringgit',
        "NGN" => 'Nigeria Naira',
        "NOK" => 'Norway Krone',
        "OMR" => 'Oman Rial',
        "PKR" => 'Pakistan Rupee',
        "PHP" => 'Philippines Peso',
        "PLN" => 'Poland Zloty',
        "RON" => 'Romania Leu',
        "ZAR" => 'South Africa Rand',
        "LKR" => 'Sri Lanka Rupee',
        "SEK" => 'Sweden Krona',
        "CHF" => 'Switzerland Franc',
        "THB" => 'Thailand Baht',
        "TRY" => 'Turkey Lira',
        "UAH" => 'Ukraine Hryvnia',
        "GBP" => 'United Kingdom Pound',
        "TWD" => 'Taiwan New Dollar',
        "VND" => 'Viet Nam Dong',
        "UZS" => 'Uzbekistan Som',
    ];

    if (!empty($sing)) {
        return $lists[$sing];
    }

    return $lists;
}

function currency()
{
    $paymentSettings = getFinancialSettings();

    return (!empty($paymentSettings) and !empty($paymentSettings['currency'])) ? $paymentSettings['currency'] : 'USD';
}

function currencySign()
{
    switch (currency()) {
        case 'USD':
            return '$';
            break;
        case 'EUR':
            return '€';
            break;
        case 'JPY':
        case 'CNY':
            return '¥';
            break;
        case 'AED':
            return 'د.إ';
            break;
        case 'SAR':
            return 'ر.س';
            break;
        case 'KRW':
            return '₩';
            break;
        case 'INR':
            return '₹';
            break;
        case 'RUB':
            return '₽';
            break;
        case 'Lek':
            return 'Lek';
            break;
        case 'AFN':
            return '؋';
            break;
        case 'ARS':
            return '$';
            break;
        case 'AWG':
            return 'ƒ';
            break;
        case 'AUD':
            return '$';
            break;
        case 'AZN':
            return '₼';
            break;
        case 'BSD':
            return '$';
            break;
        case 'BBD':
            return '$';
            break;
        case 'BYN':
            return 'Br';
            break;
        case 'BZD':
            return 'BZ$';
            break;
        case 'BMD':
            return '$';
            break;
        case 'BOB':
            return '$b';
            break;
        case 'BAM':
            return 'KM';
            break;
        case 'BWP':
            return 'P';
            break;
        case 'BGN':
            return 'лв';
            break;
        case 'BRL':
            return 'R$';
            break;
        case 'BND':
            return '$';
            break;
        case 'COP':
            return '$';
            break;
        case 'CRC':
            return '₡';
            break;
        case 'CZK':
            return 'Kč';
            break;
        case 'CUP':
            return '₱';
            break;
        case 'DKK':
            return 'kr';
            break;
        case 'DOP':
            return 'RD$';
            break;
        case 'XCD':
            return '$';
            break;
        case 'EGP':
            return '£';
            break;
        case 'GTQ':
            return 'Q';
            break;
        case 'HKD':
            return '$';
            break;
        case 'HUF':
            return 'Ft';
            break;
        case 'IDR':
            return 'Rp';
            break;
        case 'IRR':
            return '﷼';
            break;
        case 'ILS':
            return '₪';
            break;
        case 'LBP':
            return '£';
            break;
        case 'MYR':
            return 'RM';
            break;
        case 'NGN':
            return '₦';
            break;
        case 'NOK':
            return 'kr';
            break;
        case 'OMR':
            return '﷼';
            break;
        case 'PKR':
            return '₨';
            break;
        case 'PHP':
            return '₱';
            break;
        case 'PLN':
            return 'zł';
            break;
        case 'RON':
            return 'lei';
            break;
        case 'ZAR':
            return 'R';
            break;
        case 'LKR':
            return '₨';
            break;
        case 'SEK':
            return 'kr';
            break;
        case 'CHF':
            return 'CHF';
            break;
        case 'THB':
            return '฿';
            break;
        case 'TRY':
            return '₺';
            break;
        case 'UAH':
            return '₴';
            break;
        case 'GBP':
            return '£';
            break;
        case 'VND':
            return '₫';
            break;
        case 'TWD':
            return 'NT$';
            break;
        case 'UZS':
            return 'лв';
            break;
        default:
            return '$';
    }

    return '$';
}

function getCountriesMobileCode()
{
    return [
        'USA (+1)' => '+1',
        'UK (+44)' => '+44',
        'Algeria (+213)' => '+213',
        'Andorra (+376)' => '+376',
        'Angola (+244)' => '+244',
        'Anguilla (+1264)' => '+1264',
        'Antigua &amp; Barbuda (+1268)' => '+1268',
        'Argentina (+54)' => '+54',
        'Armenia (+374)' => '+374',
        'Aruba (+297)' => '+297',
        'Australia (+61)' => '+61',
        'Austria (+43)' => '+43',
        'Azerbaijan (+994)' => '+994',
        'Bahamas (+1242)' => '+1242',
        'Bahrain (+973)' => '+973',
        'Bangladesh (+880)' => '+880',
        'Barbados (+1246)' => '+1246',
        'Belarus (+375)' => '+375',
        'Belgium (+32)' => '+32',
        'Belize (+501)' => '+501',
        'Benin (+229)' => '+229',
        'Bermuda (+1441)' => '+1441',
        'Bhutan (+975)' => '+975',
        'Bolivia (+591)' => '+591',
        'Bosnia Herzegovina (+387)' => '+387',
        'Botswana (+267)' => '+267',
        'Brazil (+55)' => '+55',
        'Brunei (+673)' => '+673',
        'Bulgaria (+359)' => '+359',
        'Burkina Faso (+226)' => '+226',
        'Burundi (+257)' => '+257',
        'Cambodia (+855)' => '+855',
        'Cameroon (+237)' => '+237',
        'Canada (+1)' => '+1',
        'Cape Verde Islands (+238)' => '+238',
        'Cayman Islands (+1345)' => '+1345',
        'Central African Republic (+236)' => '+236',
        'Chile (+56)' => '+56',
        'China (+86)' => '+86',
        'Colombia (+57)' => '+57',
        'Comoros (+269)' => '+269',
        'Congo (+242)' => '+242',
        'Cook Islands (+682)' => '+682',
        'Costa Rica (+506)' => '+506',
        'Croatia (+385)' => '+385',
        'Cuba (+53)' => '+53',
        'Cyprus - North (+90)' => '+90',
        'Cyprus - South (+357)' => '+357',
        'Czech Republic (+420)' => '+420',
        'Denmark (+45)' => '+45',
        'Djibouti (+253)' => '+253',
        'Dominica (+1809)' => '+1809',
        'Dominican Republic (+1809)' => '+1809',
        'Ecuador (+593)' => '+593',
        'Egypt (+20)' => '+20',
        'El Salvador (+503)' => '+503',
        'Equatorial Guinea (+240)' => '+240',
        'Eritrea (+291)' => '+291',
        'Estonia (+372)' => '+372',
        'Ethiopia (+251)' => '+251',
        'Falkland Islands (+500)' => '+500',
        'Faroe Islands (+298)' => '+298',
        'Fiji (+679)' => '+679',
        'Finland (+358)' => '+358',
        'France (+33)' => '+33',
        'French Guiana (+594)' => '+594',
        'French Polynesia (+689)' => '+689',
        'Gabon (+241)' => '+241',
        'Gambia (+220)' => '+220',
        'Georgia (+7880)' => '+7880',
        'Germany (+49)' => '+49',
        'Ghana (+233)' => '+233',
        'Gibraltar (+350)' => '+350',
        'Greece (+30)' => '+30',
        'Greenland (+299)' => '+299',
        'Grenada (+1473)' => '+1473',
        'Guadeloupe (+590)' => '+590',
        'Guam (+671)' => '+671',
        'Guatemala (+502)' => '+502',
        'Guinea (+224)' => '+224',
        'Guinea - Bissau (+245)' => '+245',
        'Guyana (+592)' => '+592',
        'Haiti (+509)' => '+509',
        'Honduras (+504)' => '+504',
        'Hong Kong (+852)' => '+852',
        'Hungary (+36)' => '+36',
        'Iceland (+354)' => '+354',
        'India (+91)' => '+91',
        'Indonesia (+62)' => '+62',
        'Iraq (+964)' => '+964',
        'Iran (+98)' => '+98',
        'Ireland (+353)' => '+353',
        'Israel (+972)' => '+972',
        'Italy (+39)' => '+39',
        'Jamaica (+1876)' => '+1876',
        'Japan (+81)' => '+81',
        'Jordan (+962)' => '+962',
        'Kazakhstan (+7)' => '+7',
        'Kenya (+254)' => '+254',
        'Kiribati (+686)' => '+686',
        'Korea - North (+850)' => '+850',
        'Korea - South (+82)' => '+82',
        'Kuwait (+965)' => '+965',
        'Kyrgyzstan (+996)' => '+996',
        'Laos (+856)' => '+856',
        'Latvia (+371)' => '+371',
        'Lebanon (+961)' => '+961',
        'Lesotho (+266)' => '+266',
        'Liberia (+231)' => '+231',
        'Libya (+218)' => '+218',
        'Liechtenstein (+417)' => '+417',
        'Lithuania (+370)' => '+370',
        'Luxembourg (+352)' => '+352',
        'Macao (+853)' => '+853',
        'Macedonia (+389)' => '+389',
        'Madagascar (+261)' => '+261',
        'Malawi (+265)' => '+265',
        'Malaysia (+60)' => '+60',
        'Maldives (+960)' => '+960',
        'Mali (+223)' => '+223',
        'Malta (+356)' => '+356',
        'Marshall Islands (+692)' => '+692',
        'Martinique (+596)' => '+596',
        'Mauritania (+222)' => '+222',
        'Mayotte (+269)' => '+269',
        'Mexico (+52)' => '+52',
        'Micronesia (+691)' => '+691',
        'Moldova (+373)' => '+373',
        'Monaco (+377)' => '+377',
        'Mongolia (+976)' => '+976',
        'Montserrat (+1664)' => '+1664',
        'Morocco (+212)' => '+212',
        'Mozambique (+258)' => '+258',
        'Myanmar (+95)' => '+95',
        'Namibia (+264)' => '+264',
        'Nauru (+674)' => '+674',
        'Nepal (+977)' => '+977',
        'Netherlands (+31)' => '+31',
        'New Caledonia (+687)' => '+687',
        'New Zealand (+64)' => '+64',
        'Nicaragua (+505)' => '+505',
        'Niger (+227)' => '+227',
        'Nigeria (+234)' => '+234',
        'Niue (+683)' => '+683',
        'Norfolk Islands (+672)' => '+672',
        'Northern Marianas (+670)' => '+670',
        'Norway (+47)' => '+47',
        'Oman (+968)' => '+968',
        'Pakistan (+92)' => '+92',
        'Palau (+680)' => '+680',
        'Panama (+507)' => '+507',
        'Papua New Guinea (+675)' => '+675',
        'Paraguay (+595)' => '+595',
        'Peru (+51)' => '+51',
        'Philippines (+63)' => '+63',
        'Poland (+48)' => '+48',
        'Portugal (+351)' => '+351',
        'Puerto Rico (+1787)' => '+1787',
        'Qatar (+974)' => '+974',
        'Reunion (+262)' => '+262',
        'Romania (+40)' => '+40',
        'Russia (+7)' => '+7',
        'Rwanda (+250)' => '+250',
        'San Marino (+378)' => '+378',
        'Sao Tome &amp; Principe (+239)' => '+239',
        'Saudi Arabia (+966)' => '+966',
        'Senegal (+221)' => '+221',
        'Serbia (+381)' => '+381',
        'Seychelles (+248)' => '+248',
        'Sierra Leone (+232)' => '+232',
        'Singapore (+65)' => '+65',
        'Slovak Republic (+421)' => '+421',
        'Slovenia (+386)' => '+386',
        'Solomon Islands (+677)' => '+677',
        'Somalia (+252)' => '+252',
        'South Africa (+27)' => '+27',
        'Spain (+34)' => '+34',
        'Sri Lanka (+94)' => '+94',
        'St. Helena (+290)' => '+290',
        'St. Kitts (+1869)' => '+1869',
        'St. Lucia (+1758)' => '+1758',
        'Suriname (+597)' => '+597',
        'Sudan (+249)' => '+249',
        'Swaziland (+268)' => '+268',
        'Sweden (+46)' => '+46',
        'Switzerland (+41)' => '+41',
        'Syria (+963)' => '+963',
        'Taiwan (+886)' => '+886',
        'Tajikistan (+992)' => '+992',
        'Thailand (+66)' => '+66',
        'Togo (+228)' => '+228',
        'Tonga (+676)' => '+676',
        'Trinidad &amp; Tobago (+1868)' => '+1868',
        'Tunisia (+216)' => '+216',
        'Turkey (+90)' => '+90',
        'Turkmenistan (+993)' => '+993',
        'Turks &amp; Caicos Islands (+1649)' => '+1649',
        'Tuvalu (+688)' => '+688',
        'Uganda (+256)' => '+256',
        'Ukraine (+380)' => '+380',
        'United Arab Emirates (+971)' => '+971',
        'Uruguay (+598)' => '+598',
        'Uzbekistan (+998)' => '+998',
        'Vanuatu (+678)' => '+678',
        'Vatican City (+379)' => '+379',
        'Venezuela (+58)' => '+58',
        'Vietnam (+84)' => '+84',
        'Virgin Islands - British (+1)' => '+1',
        'Virgin Islands - US (+1)' => '+1',
        'Wallis &amp; Futuna (+681)' => '+681',
        'Yemen (North)(+969)' => '+969',
        'Yemen (South)(+967)' => '+967',
        'Zambia (+260)' => '+260',
        'Zimbabwe (+263)' => '+263',
    ];
}

// Truncate a string only at a whitespace
function truncate($text, $length, $withTail = true)
{
    $length = abs((int)$length);
    if (strlen($text) > $length) {
        $text = preg_replace("/^(.{1,$length})(\s.*|$)/s", ($withTail ? '\\1 ...' : '\\1'), $text);
    }

    return ($text);
}


/**
 * @param null $page => home, search, classes, categories, login, register, contact, blog, certificate_validation, 'instructors', 'organizations'
 * @return array [title, description]
 */
function getSeoMetas($page = null)
{
    return App\Models\Setting::getSeoMetas($page);
}

/**
 * @return array [title, image, link]
 */
function getSocials()
{
    return App\Models\Setting::getSocials();
}

/**
 * @return array [title, items => [title, link]]
 */
function getFooterColumns()
{
    return App\Models\Setting::getFooterColumns();
}


/**
 * @return array [site_name, site_email, site_phone, site_language, register_method, user_languages, rtl_languages, fav_icon, locale, logo, footer_logo, rtl_layout, home hero1 is active, home hero2 is active, ]
 */
function getGeneralSettings($key = null)
{
    return App\Models\Setting::getGeneralSettings($key);
}


/**
 * @param $key
 * @return array|[commission, tax, minimum_payout, currency]
 */
function getFinancialSettings($key = null)
{
    return App\Models\Setting::getFinancialSettings($key);
}


/**
 * @param string $section => 2 for hero section 2
 * @return array|[title, description, hero_background]
 */
function getHomeHeroSettings($section = '1')
{
    return App\Models\Setting::getHomeHeroSettings($section);
}

/**
 * @return array|[title, description, background]
 */
function getHomeVideoOrImageBoxSettings()
{
    return App\Models\Setting::getHomeVideoOrImageBoxSettings();
}


/**
 * @param null $page => admin_login, admin_dashboard, login, register, remember_pass, search, categories, become_instructor, certificate_validation, blog, instructors ,dashboard, panel_sidebar, user_avatar, user_cover
 * @return string|array => [all pages]
 */
function getPageBackgroundSettings($page = null)
{
    return App\Models\Setting::getPageBackgroundSettings($page);
}


/**
 * @param null $key => css, js
 * @return string|array => {css, js}
 */
function getCustomCssAndJs($key = null)
{
    return App\Models\Setting::getCustomCssAndJs($key);
}

/**
 * @return array
 */
function getSiteBankAccounts()
{
    return App\Models\Setting::getSiteBankAccounts();
}

/**
 * @return array
 */
function getOfflineBanksTitle()
{
    $titles = [];

    $banks = getSiteBankAccounts();

    if (!empty($banks) and count($banks)) {
        foreach ($banks as $bank) {
            $titles[] = $bank['title'];
        }
    }

    return $titles;
}

/**
 * @return array
 */
function getReportReasons()
{
    return App\Models\Setting::getReportReasons();
}

/**
 * @param $template {String|nullable}
 * @return array
 */
function getNotificationTemplates($template = null)
{
    return App\Models\Setting::getNotificationTemplates($template);
}

/**
 * @param $key
 * @return array
 */
function getContactPageSettings($key = null)
{
    return App\Models\Setting::getContactPageSettings($key);
}

/**
 * @param $key
 * @return array
 */
function get404ErrorPageSettings($key = null)
{
    return App\Models\Setting::get404ErrorPageSettings($key);
}

/**
 * @param $key
 * @return array
 */
function getHomeSectionsSettings($key = null)
{
    return App\Models\Setting::getHomeSectionsSettings($key);
}

/**
 * @param $key
 * @return array
 */
function getNavbarLinks()
{
    $links = App\Models\Setting::getNavbarLinksSettings();

    if (!empty($links)) {
        usort($links, function ($item1, $item2) {
            return $item1['order'] <=> $item2['order'];
        });
    }

    return $links;
}

/**
 * @return array
 */
function getPanelSidebarSettings()
{
    return App\Models\Setting::getPanelSidebarSettings();
}

/**
 * @param $page => home, search, classes, categories, login, register, contact, blog, certificate_validation, 'instructors', 'organizations'
 *
 * @return string
 * */
function getPageRobot($page)
{
    $seoSettings = getSeoMetas($page);

    return (empty($seoSettings['robot']) or $seoSettings['robot'] != 'noindex') ? 'index, follow, all' : 'NOODP, nofollow, noindex';
}


function deepClone($object)
{
    $cloned = clone($object);
    foreach ($cloned as $key => $val) {
        if (is_object($val) || (is_array($val))) {
            $cloned->{$key} = unserialize(serialize($val));
        }
    }

    return $cloned;
}

function sendNotification($template, $options, $user_id = null, $group_id = null, $sender = 'system', $type = 'single')
{
    $templateId = getNotificationTemplates($template);
    $notificationTemplate = \App\Models\NotificationTemplate::where('id', $templateId)->first();

    if (!empty($notificationTemplate)) {
        $title = str_replace(array_keys($options), array_values($options), $notificationTemplate->title);
        $message = str_replace(array_keys($options), array_values($options), $notificationTemplate->template);

        $check = \App\Models\Notification::where('user_id', $user_id)
            ->where('group_id', $group_id)
            ->where('title', $title)
            ->where('message', $message)
            ->where('sender', $sender)
            ->where('type', $type)
            ->first();

        if (empty($check) or $template != 'new_badge') {
            \App\Models\Notification::create([
                'user_id' => $user_id,
                'group_id' => $group_id,
                'title' => $title,
                'message' => $message,
                'sender' => $sender,
                'type' => $type,
                'created_at' => time()
            ]);

            if (env('APP_ENV') == 'production') {
                $user = \App\User::where('id', $user_id)->first();
                if (!empty($user) and !empty($user->email)) {
                    \Mail::to($user->email)->send(new \App\Mail\SendNotifications(['title' => $title, 'message' => $message]));
                }
            }
        }

        return true;
    }

    return false;
}

function time2string($time)
{
    $d = floor($time / 86400);
    $_d = ($d < 10 ? '0' : '') . $d;

    $h = floor(($time - $d * 86400) / 3600);
    $_h = ($h < 10 ? '0' : '') . $h;

    $m = floor(($time - ($d * 86400 + $h * 3600)) / 60);
    $_m = ($m < 10 ? '0' : '') . $m;

    $s = $time - ($d * 86400 + $h * 3600 + $m * 60);
    $_s = ($s < 10 ? '0' : '') . $s;

    return [
        'day' => $_d,
        'hour' => $_h,
        'minute' => $_m,
        'second' => $_s
    ];
}

$months = [
    1 => 'Jan.',
    2 => 'Feb.',
    3 => 'Mar.',
    4 => 'Apr.',
    5 => 'May',
    6 => 'Jun.',
    7 => 'Jul.',
    8 => 'Aug.',
    9 => 'Sep.',
    10 => 'Oct.',
    11 => 'Nov.',
    12 => 'Dec.'
];

function fromAndToDateFilter($from, $to, $query, $column = 'created_at', $strToTime = true)
{
    if (!empty($from) and !empty($to)) {
        $from = $strToTime ? strtotime($from) : $from;
        $to = $strToTime ? strtotime($to) : $to;

        $query->whereBetween($column, [$from, $to]);
    } else {
        if (!empty($from)) {
            $from = $strToTime ? strtotime($from) : $from;

            $query->where($column, '>=', $from);
        }

        if (!empty($to)) {
            $to = $strToTime ? strtotime($to) : $to;

            $query->where($column, '<', $to);
        }
    }

    return $query;
}

function random_str($length)
{
    $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $str = '';
    $max = mb_strlen($keyspace, '8bit') - 1;

    for ($i = 0; $i < $length; ++$i) {
        $str .= $keyspace[rand(0, $max)];
    }

    return $str;
}

function checkCourseForSale($course, $user)
{
    if (!$course->canSale()) {
        $toastData = [
            'title' => trans('public.request_failed'),
            'msg' => trans('cart.course_not_capacity'),
            'status' => 'error'
        ];
        return back()->with(['toast' => $toastData]);
    }

    if ($course->checkUserHasBought()) {
        $toastData = [
            'title' => trans('cart.fail_purchase'),
            'msg' => trans('site.you_bought_webinar'),
            'status' => 'error'
        ];
        return back()->with(['toast' => $toastData]);
    }

    if ($course->creator_id == $user->id or $course->teacher_id == $user->id) {
        $toastData = [
            'title' => trans('public.request_failed'),
            'msg' => trans('cart.cant_purchase_your_course'),
            'status' => 'error'
        ];
        return back()->with(['toast' => $toastData]);
    }

    $isRequiredPrerequisite = false;
    $prerequisites = $course->prerequisites;
    if (count($prerequisites)) {
        foreach ($prerequisites as $prerequisite) {
            $prerequisiteWebinar = $prerequisite->prerequisiteWebinar;

            if ($prerequisite->required and !empty($prerequisiteWebinar) and !$prerequisiteWebinar->checkUserHasBought()) {
                $isRequiredPrerequisite = true;
            }
        }
    }

    if ($isRequiredPrerequisite) {
        $toastData = [
            'title' => trans('public.request_failed'),
            'msg' => trans('cart.this_course_has_required_prerequisite'),
            'status' => 'error'
        ];
        return back()->with(['toast' => $toastData]);
    }

    return 'ok';
}
