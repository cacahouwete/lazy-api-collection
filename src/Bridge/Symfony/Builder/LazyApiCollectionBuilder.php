<?php

declare(strict_types=1);

namespace LazyApiCollection\Bridge\Symfony\Builder;

use LazyApiCollection\Model\ApiCollection;
use LazyApiCollection\Model\LazyApiCollection;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

final class LazyApiCollectionBuilder implements LazyApiCollectionBuilderInterface
{
    private HttpClientInterface $httpClient;
    private SerializerInterface $serializer;
    /**
     * @var callable
     */
    private $callbackHttp;
    /**
     * @var callable
     */
    private $callbackDeserializer;
    private int $page = 0;
    private int $itemsPerPage = 30;

    public function __construct(HttpClientInterface $httpClient, SerializerInterface $serializer)
    {
        $this->httpClient = $httpClient;
        $this->serializer = $serializer;
    }

    public function create(string $path, string $class): LazyApiCollectionBuilderInterface
    {
        $httpClient = $this->httpClient;
        $serializer = $this->serializer;

        $this->callbackHttp = static function (int $index, int $itemsPerPage) use ($httpClient, $path): ResponseInterface {
            $page = $index + 1;

            return $httpClient->request('GET', $path."?itemsPerPage=$itemsPerPage&page=$page");
        };

        $this->callbackDeserializer = static function (ResponseInterface $httpResponse) use ($serializer, $class): ApiCollection {
            return $serializer->deserialize($httpResponse->getContent(), ApiCollection::class, 'json', ['type' => $class]);
        };

        return $this;
    }

    public function withPage(int $page = 0): LazyApiCollectionBuilderInterface
    {
        $this->page = $page;

        return $this;
    }

    public function withItemsPerPage(int $itemsPerPage = 30): LazyApiCollectionBuilderInterface
    {
        $this->itemsPerPage = $itemsPerPage;

        return $this;
    }

    public function withCallbackHttp(callable $callbackHttp): LazyApiCollectionBuilderInterface
    {
        $this->callbackHttp = $callbackHttp;

        return $this;
    }

    public function withCallbackDeserializer(callable $callbackDeserializer): LazyApiCollectionBuilderInterface
    {
        $this->callbackDeserializer = $callbackDeserializer;

        return $this;
    }

    public function build(): LazyApiCollection
    {
        return new LazyApiCollection($this->callbackHttp, $this->callbackDeserializer, $this->page, $this->itemsPerPage);
    }
}
