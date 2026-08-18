<?php

namespace QmediaBy\Helpers;

class UTMHelper
{
    /**
     * @return void
     */
    public static function set(): void
    {
        foreach (static::getKeys() as $utm) {
            if (empty($_GET[$utm])) {
                continue;
            }

            $_SESSION[$utm] = $_GET[$utm];
        }
    }

    /**
     * @param string $value
     * @return string
     */
    public static function get(string $value): string
    {
        return $_SESSION[$value] ?? '';
    }

    /**
     * @return string[]
     */
    public static function getKeys(): array
    {
        return ['utm_source', 'utm_campaign', 'utm_medium', 'utm_term', 'utm_content'];
    }
}
