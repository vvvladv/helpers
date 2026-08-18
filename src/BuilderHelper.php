<?php

namespace QmediaBy\Helpers;

use Illuminate\Support\Collection;
use QmediaBy\Helpers\Contracts\ItemTransformerInterface;

class BuilderHelper
{
    /**
     * @var array<string, ItemTransformerInterface>
     */
    protected array $transformers = [];

    /**
     * @param array<string, mixed> $config
     */
    final public static function run(array $config = []): static
    {
        $params = array_merge([
            'renderTo' => 'array',
        ], $config);

        $result = evo()->runSnippet('PageBuilder', $params);
        $data = is_array($result) ? ($result[0] ?? []) : [];
        $data = is_array($data) ? $data : [];

        return new static(data: $data, config: $params);
    }

    /**
     * @param string $key
     * @param ItemTransformerInterface $transformer
     * @return $this
     */
    public function withTransformer(string $key, ItemTransformerInterface $transformer): self
    {
        $this->transformers[$key] = $transformer;

        return $this;
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
        foreach ($this->data as $key => $item) {
            if (is_array($item)) {
                $this->data[$key] = $this->processItem($item);
            }
        }

        return $this->data;
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
        protected array $config
    ) {
    }

    /**
     * @param array<string, mixed> $item
     * @return array<string, mixed>
     */
    private function processItem(array $item): array
    {
        $configKey = $item['config'] ?? null;

        if (is_string($configKey) && isset($this->transformers[$configKey])) {
            $this->transformers[$configKey]->transform($item);
        }

        return $item;
    }
}
