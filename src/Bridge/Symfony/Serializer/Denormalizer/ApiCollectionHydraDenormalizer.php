<?php

declare(strict_types=1);

namespace LazyApiCollection\Bridge\Symfony\Serializer\Denormalizer;

use LazyApiCollection\Model\ApiCollection;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class ApiCollectionHydraDenormalizer implements DenormalizerInterface, DenormalizerAwareInterface, CacheableSupportsMethodInterface
{
    use DenormalizerAwareTrait;

    /**
     * @return ApiCollection<object>
     */
    public function denormalize($data, string $type, string $format = null, array $context = []): ApiCollection
    {
        if (!\is_array($data)) {
            throw new UnexpectedValueException();
        }
        $itemType = $context['type'];

        return new ApiCollection(
            $this->denormalizer->denormalize($data['hydra:member'], $itemType.'[]', 'json', $context),
            $data['hydra:totalItems'],
        );
    }

    public function supportsDenormalization($data, string $type, string $format = null): bool
    {
        return ApiCollection::class === $type;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}
