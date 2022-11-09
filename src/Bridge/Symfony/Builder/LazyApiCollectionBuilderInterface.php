<?php

declare(strict_types=1);

namespace LazyApiCollection\Bridge\Symfony\Builder;

use LazyApiCollection\Model\LazyApiCollection;

interface LazyApiCollectionBuilderInterface
{
    public function create(string $path, string $class): self;

    public function withPage(int $page = 0): self;

    public function withItemsPerPage(int $itemsPerPage = 30): self;

    public function withCallbackHttp(callable $callbackHttp): self;

    public function withCallbackDeserializer(callable $callbackDeserializer): self;

    public function build(): LazyApiCollection;
}
