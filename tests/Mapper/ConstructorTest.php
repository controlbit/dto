<?php
declare(strict_types=1);

namespace ControlBit\Dto\Tests\Mapper;

use ControlBit\Dto\Attribute\Dto;
use ControlBit\Dto\Enum\ConstructorStrategy;
use ControlBit\Dto\Exception\InvalidArgumentException;
use ControlBit\Dto\Factory;
use ControlBit\Dto\Tests\LibraryTestCase;
use ControlBit\Dto\Tests\Resources\DtoWithConstructor;

class ConstructorTest extends LibraryTestCase
{
    public function testOptionalConstructor(): void
    {
        $from = new class() {
            public string $foo = 'foo';
        };

        $mappedObject = $this->getMapper()->map($from, DtoWithConstructor::class);

        $this->assertEquals('foofoo', $mappedObject->foo);
    }

    public function testNeverConstructorStrategy(): void
    {
        $from = new class() {
            public string $foo = 'foo';
        };

        $mappedObject = Factory::create(true, ConstructorStrategy::NEVER)->map($from, DtoWithConstructor::class);

        $this->assertEquals('foo', $mappedObject->foo);
    }

    public function testAlwaysConstructorStrategy(): void
    {
        $from = new class() {
            public string $foo = 'foo';
            public string $bar = 'bar';
        };

        $mappedObject = Factory::create(true, ConstructorStrategy::ALWAYS)->map($from, DtoWithConstructor::class);

        $this->assertEquals('foofoo', $mappedObject->foo);
        $this->assertEquals('bar', $mappedObject->bar);
    }

    public function testAlwaysConstructorWithoutAllArgumentsAvailableThrowsException(): void
    {
        $from = new class() {
            public string $foo = 'foo';
        };

        $this->expectException(InvalidArgumentException::class);

        Factory::create(true, ConstructorStrategy::ALWAYS)->map($from, DtoWithConstructor::class);
    }

    public function testOptionalConstructorWithoutAllRequiredArgumentsAvailableThrowsException(): void
    {
        $from = new class() {};

        $this->expectException(InvalidArgumentException::class);

        $this->getMapper()->map($from, DtoWithConstructor::class);
    }

    public function testAttributeOverridesDefaultStrategy(): void
    {
        $from = new #[Dto(constructorStrategy: ConstructorStrategy::NEVER)]class() {
            public string $foo = 'foo';
        };

        $mappedObject = $this->getMapper()->map($from, DtoWithConstructor::class);

        $this->assertEquals('foo', $mappedObject->foo);
    }
}