<?php

namespace QmediaBy\Helpers;

use Illuminate\Support\Collection;

class MultiFieldsHelper
{
    /**
     * @param string $string
     * @return bool
     */
    public static function is(string $string): bool
    {
        return str_contains($string, '#1') || str_contains($string, '"type"');
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public static function toArray(string $string): array
    {
        if ($string === '') {
            return [];
        }

        $values = json_decode($string, true);
        if (!is_array($values) || $values === []) {
            return [];
        }

        $array = [];

        foreach ($values as $value) {
            if (!is_array($value)) {
                continue;
            }

            $valueItem = [
                'field_type' => $value['type'] ?? null,
                'field_name' => $value['name'] ?? null,
            ];

            $items = $value['items'] ?? null;
            if (($value['type'] ?? null) === 'row' && is_array($items) && $items !== []) {
                foreach ($items as $itemKey => $item) {
                    if (!is_array($item)) {
                        continue;
                    }

                    $cleanName = $item['name'] ?? explode('#', (string) $itemKey)[0];

                    if (($item['type'] ?? null) === 'row') {
                        $valueItem[$cleanName][] = self::flattenNode($item['items'] ?? []);
                    } else {
                        $valueItem[$cleanName] = $item['value'] ?? '';
                    }
                }
            }

            $array[] = $valueItem;
        }

        return $array;
    }

    /**
     * @param string|null $string
     * @return Collection
     */
    public static function toCollection(?string $string): Collection
    {
        return collect(static::toArray($string ?? ''));
    }

    /**
     * @param array<string|int, mixed> $items
     * @return array<string, mixed>
     */
    private static function flattenNode(array $items): array
    {
        $result = [];

        foreach ($items as $key => $item) {
            if (!is_array($item)) {
                continue;
            }

            $cleanName = $item['name'] ?? explode('#', (string) $key)[0];

            if (($item['type'] ?? null) === 'row') {
                $result[$cleanName][] = self::flattenNode($item['items'] ?? []);
            } else {
                $result[$cleanName] = $item['value'] ?? '';
            }
        }

        return $result;
    }
}
