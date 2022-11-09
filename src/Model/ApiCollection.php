<?php

declare(strict_types=1);

namespace LazyApiCollection\Model;

/**
 * @template-extends \ArrayObject<int, object>
 */
final class ApiCollection extends \ArrayObject
{
    private int $totalItems;

    /**
     * @param array<object> $array
     * @param class-string  $iteratorClass
     */
    public function __construct(array $array, int $totalItems, int $flags = 0, string $iteratorClass = 'ArrayIterator')
    {
        parent::__construct($array, $flags, $iteratorClass);
        $this->totalItems = $totalItems;
    }

    public function getTotalItems(): int
    {
        return $this->totalItems;
    }
}
