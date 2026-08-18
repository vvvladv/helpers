<?php

namespace QmediaBy\Helpers;

class GlideAdapter
{
    /**
     * @param array<string, mixed> $phpthumbParams
     * @return array<string, mixed>
     */
    public static function convert(array $phpthumbParams): array
    {
        $map = [
            'w' => 'w',
            'h' => 'h',
            'q' => 'q',
            'far' => 'fit',
            'zc' => 'fit',
            'bg' => 'bg',
            'f' => 'fm',
        ];

        $glideParams = [];

        foreach ($phpthumbParams as $key => $value) {
            if (!isset($map[$key])) {
                continue;
            }

            if ($key === 'zc') {
                $glideParams['fit'] = 'crop';
                continue;
            }

            if ($key === 'far') {
                $glideParams['fit'] = 'fill';
                continue;
            }

            $glideParams[$map[$key]] = $value;
        }

        return $glideParams;
    }
}
