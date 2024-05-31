<?php

namespace ControlBit\Dto\Contract\Mapper;

use ControlBit\Dto\Contract\Accessor\SetterInterface;
use ControlBit\Dto\Mapper\Mapper;

interface ValueConverterInterface
{
    public function supports(SetterInterface $setter, mixed $value): bool;

    public function execute(Mapper $mapper, SetterInterface $setter, mixed $value): mixed;
}