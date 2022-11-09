<?php

declare(strict_types=1);

namespace Tests\LazyApiCollection\Model;

use LazyApiCollection\Model\ApiCollection;
use LazyApiCollection\Model\LazyApiCollection;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class LazyApiCollectionTest extends TestCase
{
    use ProphecyTrait;

    public function testExecute(): LazyApiCollection
    {
        $items = [];
        $response = new \stdClass();
        $httpCallback = static function (int $index, int $nbItem) use (&$response, &$items): \stdClass {
            $items[] = $index;

            return $response;
        };

        $expectedItem1 = new \stdClass();
        $expectedItem2 = new \stdClass();
        $expectedItem3 = new \stdClass();

        $apiCollection = new ApiCollection([$expectedItem1, $expectedItem2, $expectedItem3], 100);
        $denoCallback = static function (\stdClass $data) use ($apiCollection): ApiCollection {
            return $apiCollection;
        };
        $object = new LazyApiCollection($httpCallback, $denoCallback, 0, 3);

        self::assertSame($expectedItem1, $object->current());
        self::assertSame(100, $object->getTotalItems());
        self::assertCount(100, $object);
        self::assertSame(34, $object->getTotalPages());
        self::assertSame(0, $object->key());
        self::assertTrue($object->valid());
        self::assertCount(1, $items);
        self::assertSame([0], $items);

        $object->next();
        self::assertSame(1, $object->key());
        self::assertCount(1, $items);
        self::assertSame([0], $items);

        $object->next();
        self::assertSame(2, $object->key());
        self::assertCount(1, $items);
        self::assertSame([0], $items);

        $object->next();
        self::assertSame(3, $object->key());
        self::assertCount(4, $items);
        self::assertSame([0, 1, 2, 3], $items);

        return $object;
    }
}
