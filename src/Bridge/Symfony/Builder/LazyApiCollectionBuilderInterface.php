<?php

declare(strict_types=1);

namespace LazyApiCollection\Bridge\Symfony\Builder;

use LazyApiCollection\Model\LazyApiCollection;

/**
 * @psalm-template T
 */
interface LazyApiCollectionBuilderInterface
{
    /**
     * @return LazyApiCollectionBuilderInterface<T>
     */
    public function create(string $path, string $class): self;

    /**
     * @return LazyApiCollectionBuilderInterface<T>
     */
    public function withPage(int $page = 0): self;

    /**
     * @return LazyApiCollectionBuilderInterface<T>
     */
    public function withItemsPerPage(int $itemsPerPage = 30): self;

    /**
     * @return LazyApiCollectionBuilderInterface<T>
     */
    public function withCallbackHttp(callable $callbackHttp): self;

    /**
     * @return LazyApiCollectionBuilderInterface<T>
     */
    public function withCallbackDeserializer(callable $callbackDeserializer): self;

    /**
     * @return LazyApiCollection<T>
     */
    public function build(): LazyApiCollection;
}
