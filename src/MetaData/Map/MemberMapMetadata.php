<?php
declare(strict_types=1);

namespace ControlBit\Dto\MetaData\Map;

use ControlBit\Dto\Contract\Transformer\TransformableInterface;
use ControlBit\Dto\Enum\MappingDirection;

final class MemberMapMetadata implements TransformableInterface
{
    public function __construct(
        private readonly string           $sourceMember,
        private readonly string           $destinationMember,
        private readonly ?string          $transformer = null,
        private readonly MappingDirection $mappingDirection = MappingDirection::FROM,
        private bool                      $mappedInConstructor = false,
    ) {
    }

    public function getSourceMember(): string
    {
        return $this->sourceMember;
    }

    public function getMappingDirection(): MappingDirection
    {
        return $this->mappingDirection;
    }

    public function getDestinationMember(): string
    {
        return $this->destinationMember;
    }

    public function getTransformerClassOrId(): ?string
    {
        return $this->transformer;
    }

    public function hasTransformer(): bool
    {
        return $this->transformer !== null;
    }

    public function setMappedInConstructor(): self
    {
        $this->mappedInConstructor = true;

        return $this;
    }

    public function isMappedInConstructor(): bool
    {
        return $this->mappedInConstructor;
    }
}