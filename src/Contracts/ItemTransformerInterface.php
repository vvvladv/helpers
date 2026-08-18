<?php

namespace QmediaBy\Helpers\Contracts;

interface ItemTransformerInterface
{
    /**
     * @param array $item
     * @return void
     */
    public function transform(array &$item): void;
}
