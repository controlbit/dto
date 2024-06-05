<?php
declare(strict_types=1);

namespace ControlBit\Dto\Mapper\ValueConverter;

use ControlBit\Dto\Contract\Accessor\SetterInterface;
use ControlBit\Dto\Contract\Mapper\ValueConverterInterface;
use ControlBit\Dto\Mapper\Mapper;
use Symfony\Component\Uid\AbstractUid;
use Symfony\Component\Uid\Uuid;

final class EntityIdentifier implements ValueConverterInterface
{
    public function supports(SetterInterface $setter, mixed $value): bool
    {
        if (!$setter->getType()->hasInstanceOf(AbstractUid::class)) {
            return false;
        }

        if (\is_string($value) && Uuid::isValid($value)) {
            return true;
        }

        return false;
    }

    /**
     * @param  string  $value
     */
    public function execute(Mapper $mapper, SetterInterface $setter, mixed $value): mixed
    {
        /** @var class-string<AbstractUid> $abstractClass */
        $abstractClass = $setter->getType()->getInstanceOf(AbstractUid::class);

        return $abstractClass::fromString($value);
    }
}