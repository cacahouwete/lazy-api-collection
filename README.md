LazyApiCollection
=================

PHP tool to have an easy way to iterate an api with multiple pages

## Installation with symfony

```shell
composer require cacahouwete/lazy-api-collection 
```

Add bundle in your symfony project

```php
<?php
// config/bundles.php

return [
    ...
    LazyApiCollection\Bridge\Symfony\LazyApiCollectionBundle::class => ['all' => true],
]
```

## Basic usage with symfony

```php
<?php
namespace App\ApiEntity;

// src/ApiEntity/Dummy.php
final class Dummy {
    public string $field1;
    public string $field2;
    ....
}
```

```php
// src/ApiRepository/DummyApiRepository.php
namespace App\ApiRepository;

use LazyApiCollection\Bridge\Symfony\Builder\LazyApiCollectionBuilderInterface;
use LazyApiCollection\Model\ApiCollection;
use LazyApiCollection\Model\LazyApiCollection;

final class DummyApiRepository
{
    private const PATH = '/api/dummies';
    
    private LazyApiCollectionBuilderInterface $lazyApiCollectionBuilder;
    private string $targetUrl;

    public function __construct(LazyApiCollectionBuilderInterface $lazyApiCollectionBuilder, string $targetUrl)
    {
        $this->lazyApiCollectionBuilder = $lazyApiCollectionBuilder;
        $this->targetUrl = $targetUrl;
    }

    /**
     * @return iterable<Dummy>
     */
    public function findAllByPageAndNbItem(): iterable
    {
        return $this->lazyApiCollectionBuilder
            ->create($this->targetUrl.self::PATH, Dummy::class)
            ->build()
        ;
    }
}
```
