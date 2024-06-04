<?php
declare(strict_types=1);

namespace ControlBit\Dto\Mapper;

use ControlBit\Dto\Contract\Accessor\SetterInterface;
use ControlBit\Dto\Contract\Mapper\ValueConverterInterface;
use ControlBit\Dto\Contract\Transformer\TransformableInterface;
use ControlBit\Dto\Contract\Transformer\TransformerInterface;
use ControlBit\Dto\Exception\InvalidArgumentException;
use ControlBit\Dto\MetaData\Class\ClassMetadata;
use ControlBit\Dto\MetaData\Map\MemberMapMetadata;
use Symfony\Component\DependencyInjection\ContainerInterface;

final readonly class ValueConverter
{
    /**
     * @param  iterable<ValueConverterInterface>  $valueConverters
     */
    public function __construct(
        private iterable            $valueConverters,
        private ?ContainerInterface $container = null,
    ) {
    }

    public function map(
        Mapper            $mapper,
        ClassMetadata     $sourceMetadata,
        SetterInterface   $setter,
        MemberMapMetadata $memberMapMetadata,
        mixed             $value,
    ): mixed {
        $value = $this->transform($value, $sourceMetadata, $memberMapMetadata);

        foreach ($this->valueConverters as $valueConverter) {
            if (!$valueConverter->supports($setter, $value)) {
                continue;
            }

            $value = $valueConverter->execute($mapper, $setter, $value);
        }

        if ($setter instanceof TransformableInterface) {
            $value = $this->transform($value, $sourceMetadata, $setter);
        }

        return $value;
    }

    private function transform(
        mixed                  $value,
        ClassMetadata          $sourceMetadata,
        TransformableInterface $transformable,
    ): mixed {
        if (!$transformable->hasTransformer()) {
            return $value;
        }

        $transformerClassOrId = $transformable->getTransformerClassOrId();
        $transformer          = $this->instantiateTransformer($transformerClassOrId); // @phpstan-ignore-line

        if ($sourceMetadata->isDoctrineEntity()) {
            return $transformer->reverse($value);
        }

        return $transformer->transform($value);
    }

    /**
     * @param  string|class-string  $transformerClassOrId
     */
    private function instantiateTransformer(string $transformerClassOrId): TransformerInterface
    {
        if (null !== $this->container) {
            $transformerService = $this->container->get($transformerClassOrId);
        }

        if (isset($transformerService)) {
            $this->validateTransformer($transformerService);

            /** @var TransformerInterface $transformerService */
            return $transformerService;
        }

        $this->validateTransformer($transformerClassOrId);

        /* @phpstan-ignore-next-line */
        return (new \ReflectionClass($transformerClassOrId))->newInstanceWithoutConstructor();
    }

    private function validateTransformer(object|string $transformerClassOrObject): void
    {
        if (\is_string($transformerClassOrObject) && !\class_exists($transformerClassOrObject)) {
            throw new InvalidArgumentException(
                \sprintf(
                    'Transformer class "%s" does not exists.',
                    $transformerClassOrObject,
                )
            );
        }

        if (\is_a($transformerClassOrObject, TransformerInterface::class, true)) {
            return;
        }

        throw new InvalidArgumentException(
            \sprintf(
                'Transformer "%s" must implement "%s".',
                \is_object($transformerClassOrObject) ? $transformerClassOrObject::class : $transformerClassOrObject,
                TransformerInterface::class,
            )
        );
    }
}