<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class TimeZoneHelper
{
    public static function all()
    {
        return Cache::rememberForever('timezones_list_collection', function () {
            $timestamp = time();
            $timezone = [];

            foreach (timezone_identifiers_list(\DateTimeZone::ALL) as $key => $value) {
                date_default_timezone_set($value);
                $timezone[$value] = $value . ' (' . date('P', $timestamp) . ')';
            }

            return collect($timezone)->sortKeys();
        });
    }

    public static function russian(bool $forAutocomplete = false): array
    {
        $timezones = [
            'Asia/Anadyr' => 'Анадырь (UTC +12:00)',
            'Asia/Barnaul' => 'Барнаул (UTC +07:00)',
            'Asia/Chita' => 'Чита (UTC +09:00)',
            'Asia/Irkutsk' => 'Иркутск (UTC +08:00)',
            'Asia/Kamchatka' => 'Петропавловск-Камчатский (UTC +12:00)',
            'Asia/Khandyga' => 'Хандыга (UTC +09:00)',
            'Asia/Krasnoyarsk' => 'Красноярск (UTC +07:00)',
            'Asia/Magadan' => 'Магадан (UTC +11:00)',
            'Asia/Novokuznetsk' => 'Новокузнецк (UTC +07:00)',
            'Asia/Novosibirsk' => 'Новосибирск (UTC +07:00)',
            'Asia/Omsk' => 'Омск (UTC +06:00)',
            'Asia/Sakhalin' => 'Южно-Сахалинск (UTC +11:00)',
            'Asia/Srednekolymsk' => 'Среднеколымск (UTC +11:00)',
            'Asia/Tomsk' => 'Томск (UTC +07:00)',
            'Asia/Ust-Nera' => 'Усть-Нера (UTC +10:00)',
            'Asia/Vladivostok' => 'Владивосток (UTC +10:00)',
            'Asia/Yakutsk' => 'Якутск (UTC +09:00)',
            'Asia/Yekaterinburg' => 'Екатеринбург (UTC +05:00)',
            'Europe/Astrakhan' => 'Астрахань (UTC +04:00)',
            'Europe/Kaliningrad' => 'Калининград (UTC +02:00)',
            'Europe/Kirov' => 'Киров (UTC +03:00)',
            'Europe/Moscow' => 'Москва (UTC +03:00)',
            'Europe/Samara' => 'Самара (UTC +04:00)',
            'Europe/Saratov' => 'Саратов (UTC +04:00)',
            'Europe/Simferopol' => 'Симферополь (UTC +03:00)',
            'Europe/Ulyanovsk' => 'Ульяновск (UTC +04:00)',
            'Europe/Volgograd' => 'Волгоград (UTC +03:00)',
            'Asia/Khabarovsk' => 'Хабаровск (UTC +10:00)',
            'Asia/Blagoveshchensk' => 'Благовещенск (UTC +09:00)',
            'Asia/Ufa' => 'Уфа (UTC +05:00)',
            'Asia/Ulaanbaatar' => 'Улан-Удэ (UTC +08:00)'
        ];

        if ($forAutocomplete) {
            $result = [];

            foreach ($timezones as $key => $timezone) {
                $result[] = [
                    'key' => $key,
                    'value' => $timezone
                ];
            }

            return $result;
        }

        return $timezones;
    }
}
