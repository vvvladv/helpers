<?php

namespace QmediaBy\Helpers;

use QmediaBy\Helpers\Support\Package;

class ThumbHelper
{
    /**
     * @param string $image
     * @param string $options
     * @param bool $returnNoImageThumb
     * @return string
     */
    public static function make(
        string $image,
        string $options = 'f=webp,q=95',
        bool $returnNoImageThumb = true
    ): string {
        $engine = (string) Package::config('thumbs.engine', 'glide');

        return $engine === 'phpthumb'
            ? self::processThumb($image, $options, $returnNoImageThumb)
            : self::processGlide($image, $options, $returnNoImageThumb);
    }

    /**
     * @param string $image
     * @param string $options
     * @param bool $returnNoImageThumb
     * @return string
     */
    private static function processGlide(
        string $image,
        string $options = 'f=webp,q=95',
        bool $returnNoImageThumb = true
    ): string {
        $queryString = str_replace([' ', ','], ['', '&'], $options);
        parse_str($queryString, $paramsArray);

        return GlideHelper::process(
            $image,
            GlideAdapter::convert(is_array($paramsArray) ? $paramsArray : []),
            $returnNoImageThumb
        );
    }

    /**
     * @param string $image
     * @param string $options
     * @param bool $returnNoImageThumb
     * @return string
     */
    private static function processThumb(
        string $image,
        string $options = 'f=webp,q=95',
        bool $returnNoImageThumb = true
    ): string {
        $image = ltrim($image, '/');

        if ($image === '' && $returnNoImageThumb === false) {
            return '';
        }

        $absolute = function_exists('evo')
            ? evo()->getConfig('base_path') . $image
            : Package::publicPath($image);

        if (!file_exists($absolute) && $returnNoImageThumb === false) {
            return '';
        }

        return (string) evo()->runSnippet('phpthumb', [
            'input' => $image,
            'options' => $options,
            'noImage' => Package::noImage(),
        ]);
    }
}
