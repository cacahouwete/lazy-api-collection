<?php

declare(strict_types=1);

namespace LazyApiCollection\Model;

use UnexpectedValueException;

/**
 * @template-implements  \Iterator<object>
 */
final class LazyApiCollection implements \Iterator, \Countable
{
    private bool $initialized = false;
    /**
     * @var array<int, mixed>
     */
    private array $httpResponses = [];
    /**
     * @var array<int, ApiCollection>
     */
    private array $pages = [];
    /**
     * @var callable
     */
    private $callbackHttp;
    /**
     * @var callable
     */
    private $callbackDeserializer;
    private int $pageIndex;
    private int $itemIndex = 0;
    private int $itemsPerPage;
    private int $totalItems;
    private int $totalPages;

    public function __construct(callable $callbackHttp, callable $callbackDeserializer, int $pageIndex = 0, int $itemsPerPage = 30)
    {
        $this->callbackHttp = $callbackHttp;
        $this->callbackDeserializer = $callbackDeserializer;
        $this->pageIndex = $pageIndex;
        $this->itemsPerPage = $itemsPerPage;
        $this->initialize();
    }

    public function isInitialized(): bool
    {
        return $this->initialized;
    }

    public function getTotalItems(): int
    {
        return $this->totalItems;
    }

    public function getTotalPages(): int
    {
        return $this->totalPages;
    }

    public function loadPage(int $pageIndex = 0, ?int $limit = null): void
    {
        $callbackHttp = $this->callbackHttp;
        while ((null === $limit && $pageIndex < $this->totalPages) || ($pageIndex < $limit && $pageIndex < $this->totalPages)) {
            if (!\array_key_exists($pageIndex, $this->httpResponses) && !\array_key_exists($pageIndex, $this->pages)) {
                $this->httpResponses[$pageIndex] = $callbackHttp($pageIndex, $this->itemsPerPage);
            }
            ++$pageIndex;
        }
    }

    public function current(): object
    {
        $page = $this->getPage($this->pageIndex);

        if (null === $page) {
            throw new UnexpectedValueException();
        }

        $item = $page[$this->itemIndex];

        if (null === $item) {
            throw new UnexpectedValueException();
        }

        return $item;
    }

    public function next(): void
    {
        $page = $this->getPage($this->pageIndex);

        if (null === $page) {
            return;
        }
        ++$this->itemIndex;
        if ($page->offsetExists($this->itemIndex)) {
            return;
        }
        if ($this->itemIndex !== $this->itemsPerPage) {
            return;
        }
        $this->itemIndex = 0;

        $this->removePage($this->pageIndex);

        ++$this->pageIndex;

        $this->loadPage($this->pageIndex, $this->pageIndex + 3);
    }

    public function key(): ?int
    {
        return $this->pageIndex * $this->itemsPerPage + $this->itemIndex;
    }

    public function valid(): bool
    {
        $page = $this->getPage($this->pageIndex);

        if (null === $page) {
            return false;
        }

        return $page->offsetExists($this->itemIndex);
    }

    public function rewind(): void
    {
        $this->pageIndex = 0;
        $this->itemIndex = 0;
    }

    public function count(): int
    {
        return $this->totalItems;
    }

    private function preInit(): void
    {
        $callbackHttp = $this->callbackHttp;
        $this->httpResponses[$this->pageIndex] = $callbackHttp($this->pageIndex, $this->itemsPerPage);
    }

    private function initialize(): void
    {
        if (!\array_key_exists($this->pageIndex, $this->httpResponses)) {
            $this->preInit();
        }
        $this->denormalizePage($this->pageIndex);
        $page = $this->pages[$this->pageIndex];
        $this->totalItems = $page->getTotalItems();
        $this->totalPages = (int) ceil($this->totalItems / $this->itemsPerPage);
        $this->initialized = true;
    }

    private function denormalizePage(int $index): void
    {
        $callback = $this->callbackDeserializer;
        $this->pages[$index] = $callback($this->httpResponses[$index]);
    }

    private function getPage(int $index): ?ApiCollection
    {
        if (!\array_key_exists($index, $this->pages)) {
            if (!\array_key_exists($index, $this->httpResponses)) {
                return null;
            }
            $this->denormalizePage($index);
        }

        return $this->pages[$index];
    }

    /**
     * Free memory to avoid having too much memory usage.
     */
    private function removePage(int $index): void
    {
        unset($this->pages[$index], $this->httpResponses[$index]);
    }
}
