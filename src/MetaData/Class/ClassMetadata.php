<?php
declare(strict_types=1);

namespace ControlBit\Dto\MetaData\Class;

use ControlBit\Dto\Attribute\Identifier;
use ControlBit\Dto\Bag\AttributeBag;
use ControlBit\Dto\Bag\MethodBag;
use ControlBit\Dto\Bag\PropertyBag;
use ControlBit\Dto\Contract\AttributedInterface;
use ControlBit\Dto\MetaData\Method\MethodMetadata;
use ControlBit\Dto\MetaData\Property\PropertyMetadata;
use Doctrine\ORM\Mapping\Entity;

final readonly class ClassMetadata implements AttributedInterface
{
    public function __construct(
        private string       $fqcn,
        private AttributeBag $attributes,
        private PropertyBag  $properties,
        private MethodBag    $methodsMetadata,
    ) {
    }

    public function getFqcn(): string
    {
        return $this->fqcn;
    }

    public function getProperties(): PropertyBag
    {
        return $this->properties;
    }

    public function getProperty(string $propertyName): ?PropertyMetadata
    {
        return $this->properties->get($propertyName);
    }

    public function getMethod(string $methodName): ?MethodMetadata
    {
        return $this->methodsMetadata->get($methodName);
    }

    public function getAttributes(): AttributeBag
    {
        return $this->attributes;
    }

    public function isDoctrineEntity(): bool
    {
        return $this->attributes->has(Entity::class);
    }

    public function getIdentifierProperty(): ?PropertyMetadata
    {
        foreach ($this->properties as $property) {
            if ($property->getAttributes()->has(Identifier::class)) {
                return $property;
            }
        }

        return null;
    }
}