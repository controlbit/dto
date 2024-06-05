<?php
declare(strict_types=1);

namespace ControlBit\Dto\Finder\SetterType;

use ControlBit\Dto\Accessor\Setter\MethodSetter;
use ControlBit\Dto\Bag\AttributeBag;
use ControlBit\Dto\Bag\TypeBag;
use ControlBit\Dto\Contract\Accessor\SetterInterface;
use ControlBit\Dto\Contract\Mapper\SetterResolverInterface;
use ControlBit\Dto\MetaData\Class\ClassMetadata;
use ControlBit\Dto\MetaData\Map\MemberMapMetadata;
use ControlBit\Dto\Util\TypeTool;
use function ControlBit\Dto\instantiate_attributes;

final class ViaSetter implements SetterResolverInterface
{
    public function resolve(
        ClassMetadata     $destinationMetaData,
        MemberMapMetadata $memberMapMetadata,
    ): ?SetterInterface {

        $method = $destinationMetaData->getMethod($memberMapMetadata->getDestinationMember())
                  ?? $destinationMetaData->getMethod('set'.\ucfirst($memberMapMetadata->getDestinationMember()));

        if (null === $method) {
            return null;
        }

        $reflectionMethod = new \ReflectionMethod($destinationMetaData->getFqcn(), $method->getName());
        $param            = $reflectionMethod->getParameters()[0] ?? null;
        $type             = new TypeBag(null !== $param ? TypeTool::getReflectionTypes($param) : []);

        return new MethodSetter(
            $reflectionMethod->getName(),
            $type,
            AttributeBag::fromArray(instantiate_attributes($reflectionMethod)),
        );
    }
}