<?php
declare(strict_types=1);

namespace ControlBit\Dto\Util;

use ControlBit\Dto\MetaData\Class\ClassMetadata;
use ControlBit\Dto\MetaData\Property\PropertyMetadata;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Will safely initialize Values on object by provided MetaData.
 */
final class Initializer
{
    private function __construct()
    {
        // noop
    }

    public static function autoInitialize(object $object, ClassMetadata $objectMetadata): void
    {
        foreach ($objectMetadata->getProperties() as $propertyMetadata) {
            self::autoInitializeProperty($object, $propertyMetadata);
        }
    }

    private static function autoInitializeProperty(object $object, PropertyMetadata $propertyMetadata): void
    {
        if ((new \ReflectionObject($object))->getProperty($propertyMetadata->getName())->isInitialized($object)) {
            return;
        }

        $accessor = $propertyMetadata->getAccessor();
        $types    = $propertyMetadata->getType();

        if ($types->hasNull()) {
            $accessor->getSetter()?->set($object, null);

            return;
        }

        if ($types->hasIterable()) {
            $accessor->getSetter()?->set($object, []);

            return;
        }

        if ($types->hasOnlyOneClass(ArrayCollection::class)) {
            $accessor->getSetter()?->set($object, new ArrayCollection());
        }
    }
}
