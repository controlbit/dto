<?php
declare(strict_types=1);

namespace ControlBit\Dto\Finder\SetterType;

use ControlBit\Dto\Contract\Accessor\SetterInterface;
use ControlBit\Dto\Contract\Mapper\SetterResolverInterface;
use ControlBit\Dto\MetaData\Class\ClassMetadata;
use ControlBit\Dto\MetaData\Map\MemberMapMetadata;

final class Direct implements SetterResolverInterface
{
    public function __construct()
    {
    }

    public function resolve(
        ClassMetadata    $destinationMetaData,
        MemberMapMetadata $memberMapMetadata
    ): ?SetterInterface {

        $toPropertyMetadata = $destinationMetaData->getProperty($memberMapMetadata->getDestinationMember());

        return $toPropertyMetadata?->getAccessor()->getSetter();
    }
}