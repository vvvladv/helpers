<?php

namespace QmediaBy\Helpers;

use EvolutionCMS\Facades\UrlProcessor;

class FunctionsHelper
{
    /**
     * @param string|int $value
     * @param bool $full
     * @return string
     */
    public static function url(string|int $value, bool $full = false): string
    {
        if (!is_numeric($value)) {
            return (string) $value;
        }

        $mode = $full ? 'full' : '';

        return UrlProcessor::makeUrl($value, '', '', $mode);
    }

    /**
     * @param string $date
     * @return string
     */
    public static function months(string $date): string
    {
        if ($date === '') {
            return '';
        }

        $enMonth = [
            'January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December',
        ];
        $ruMonth = [
            'января', 'февраля', 'марта', 'апреля', 'мая', 'июня',
            'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря',
        ];

        return str_replace($enMonth, $ruMonth, $date);
    }
}
