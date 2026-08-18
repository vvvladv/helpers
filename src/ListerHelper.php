<?php

namespace QmediaBy\Helpers;

use Illuminate\Support\Collection;
use QmediaBy\Helpers\Support\PaginationTrait;

class ListerHelper
{
    use PaginationTrait;

    /**
     * @param array<string, mixed> $config
     */
    final public static function run(array $config = [], string $snippet = 'DocLister'): static
    {
        $raw = (string) evo()->runSnippet($snippet, array_merge([
            'api' => 1,
            'idType' => 'documents',
            'orderBy' => 'menuindex ASC',
            'tvPrefix' => '',
            'urlScheme' => 'full',
        ], $config));

        $data = json_decode($raw, true);
        $data = is_array($data) ? $data : [];

        if ($data !== [] && !empty($config['removeFirst'])) {
            $data = (array) ($data[0] ?? []);
        }

        return new static($data, $config, $snippet);
    }

    /**
     * @return array<int|string, mixed>
     */
    public function paginate(): array
    {
        if (($this->config['paginate'] ?? null) !== 'pages') {
            return $this->toArray();
        }

        $saveDlObject = $this->config['saveDLObject'] ?? null;
        if (empty($saveDlObject)) {
            return $this->toArray();
        }

        $_DL = evo()->getPlaceholder($saveDlObject);
        $paginatorExtender = is_object($_DL) ? $_DL->getExtender('paginate') : null;

        if (empty($paginatorExtender)) {
            return $this->toArray();
        }

        $paginator = $this->paginator(
            $this->toCollection(),
            (int) ($this->config['display'] ?? 0),
            (int) $paginatorExtender->totalDocs()
        );

        return $paginator->toArray();
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return (string) json_encode($this->toArray(), JSON_UNESCAPED_UNICODE);
    }

    /**
     * @return array<int|string, mixed>
     */
    public function toArray(): array
    {
        return array_values($this->data);
    }

    /**
     * @return Collection
     */
    public function toCollection(): Collection
    {
        return collect($this->toArray());
    }

    /**
     * @param array<int|string, mixed> $data
     * @param array<string, mixed> $config
     */
    protected function __construct(
        protected array $data,
        protected array $config,
        protected string $snippet
    ) {
    }
}
