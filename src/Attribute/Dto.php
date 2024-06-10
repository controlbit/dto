<?php

declare(strict_types=1);

namespace ControlBit\Dto\Attribute;

use ControlBit\Dto\Enum\ConstructorStrategy;

/**
 * Denotes when Given Object is DTO, on property, when mapping, or
 * with argument, on Controller injection, when using Symfony.
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_PROPERTY | \Attribute::TARGET_PARAMETER | \Attribute::IS_REPEATABLE)]
class Dto
{
    /**
     * @param  class-string|null  $class
     * @param  class-string|null  $entityClass
     */
    public function __construct(
        public readonly ?string              $class = null,
        private readonly ?string             $entityClass = null,
        private readonly ConstructorStrategy $constructorStrategy = ConstructorStrategy::OPTIONAL,
    )
    {
    }

    /**
     * @return class-string|null
     */
    public function getClass(): ?string
    {
        return $this->class;
    }

    /**
     * @return class-string|null
     */
    public function getEntityClass(): ?string
    {
        return $this->entityClass;
    }

    public function getConstructorStrategy(): ConstructorStrategy
    {
        return $this->constructorStrategy;
    }
}