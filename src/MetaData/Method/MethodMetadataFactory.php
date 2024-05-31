<?php
declare(strict_types=1);

namespace ControlBit\Dto\MetaData\Method;

use ControlBit\Dto\Bag\AttributeBag;
use ControlBit\Dto\Bag\TypeBag;
use ControlBit\Dto\Util\TypeTool;
use function ControlBit\Dto\instantiate_attributes;

final class MethodMetadataFactory
{
    public function create(object $object, string $methodName): MethodMetadata
    {
        $reflectionObject = new \ReflectionObject($object);
        $reflectionMethod = $reflectionObject->getMethod($methodName);

        return new MethodMetadata(
            $reflectionMethod->name,
            new TypeBag(TypeTool::getReflectionTypes($reflectionMethod)),
            AttributeBag::fromArray(instantiate_attributes($reflectionMethod)),
        );
    }
}