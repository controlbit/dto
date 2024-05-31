<?php

declare(strict_types=1);

namespace ControlBit\Dto\Attribute;

use ControlBit\Dto\Contract\Transformer\TransformerInterface;
use ControlBit\Dto\Exception\InvalidArgumentException;

/**
 * Defines a transformer on property.
 * Transformer will be called before value is
 * applied to destination property.
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
readonly class Transformer
{
    /**
     * @var class-string<TransformerInterface>
     */
    private string $transformerClass;

    /**
     * @param  class-string  $transformerClass
     */
    public function __construct(string $transformerClass)
    {
        if (!\class_exists($transformerClass)) {
            throw new InvalidArgumentException(\sprintf('Transformer Class "%s" does not exist.', $transformerClass));
        }

        if (!is_a($transformerClass, TransformerInterface::class, true)) {
            throw new InvalidArgumentException(
                \sprintf(
                    'Class "%s" does not transformer. It must implement "%s".',
                    $transformerClass,
                    TransformerInterface::class
                )
            );
        }

        $this->transformerClass = $transformerClass;
    }

    /**
     * @return class-string<TransformerInterface>
     */
    public function getTransformerIdOrClass(): string
    {
        return $this->transformerClass;
    }
}