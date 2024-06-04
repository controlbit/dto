<?php
declare(strict_types=1);

namespace ControlBit\Dto\MetaData\Map;

use ControlBit\Dto\Attribute\MapFrom;
use ControlBit\Dto\Attribute\MapTo;
use ControlBit\Dto\Attribute\Transformer;
use ControlBit\Dto\Bag\AttributeBag;
use ControlBit\Dto\MetaData\Class\ClassMetadata;
use ControlBit\Dto\MetaData\Property\PropertyMetadata;

final class MapMetadataFactory
{
    public function create(ClassMetadata $classMetadata): MapMetadataCollection
    {
        $mapMetadata = new MapMetadataCollection();

        foreach ($classMetadata->getProperties() as $propertyMetadata) {
            $attributes = $propertyMetadata->getAttributes();

            $memberMetadata = match (true) {
                $attributes->has(MapTo::class)   => $this->mapTo($propertyMetadata, $attributes),
                $attributes->has(MapFrom::class) => $this->mapFrom($propertyMetadata, $attributes),
                default                          => new MemberMapMetadata(
                    $propertyMetadata->getName(),
                    $propertyMetadata->getName(),
                    $this->getTransformer($propertyMetadata)
                ),
            };

            $mapMetadata->add($memberMetadata);
        }

        return $mapMetadata;
    }

    private function mapTo(PropertyMetadata $propertyMetadata, AttributeBag $attributes): MemberMapMetadata
    {
        /** @var MapTo $attribute */
        $attribute = $attributes->get(MapTo::class);

        return new MemberMapMetadata(
            $propertyMetadata->getName(),
            $attribute->getMember(),
            $this->getTransformer($propertyMetadata),
        );
    }

    private function mapFrom(PropertyMetadata $propertyMetadata, AttributeBag $attributes): MemberMapMetadata
    {
        /** @var MapFrom $attribute */
        $attribute = $attributes->get(MapFrom::class);

        return new MemberMapMetadata(
            $propertyMetadata->getName(),
            $attribute->getMember(),
            $this->getTransformer($propertyMetadata),
        );
    }

    private function getTransformer(PropertyMetadata $propertyMetadata): ?string
    {
        foreach ($propertyMetadata->getAttributes() as $attribute) {
            if (!is_a($attribute, Transformer::class, true)) {
                continue;
            }

            return $attribute->getTransformerIdOrClass();
        }

        return null;
    }
}