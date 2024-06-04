<?php
declare(strict_types=1);

namespace ControlBit\Dto\Contract\Transformer;

interface TransformableInterface
{
    /**
     * @return class-string|string|null
     */
    public function getTransformerClassOrId(): ?string;

    public function hasTransformer(): bool;
}