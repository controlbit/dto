<?php

namespace ControlBit\Dto\Tests\Mapper\Basic;

use ControlBit\Dto\Exception\PropertyMapException;
use ControlBit\Dto\Tests\LibraryTestCase;

class TypedTest extends LibraryTestCase
{
    public function testBasicPropertyMappingFromObjectToObject(): void
    {
        $from = new class() {
            public string $foo = 'foo';
        };

        $to = new class() {
            public string $foo;
        };

        $mappedObject = $this->getMapper()->map($from, $to);

        $this->assertEquals('foo', $mappedObject->foo);
    }

    public function testBasicPropertyMappingFromObjectToClass(): void
    {
        $from = new class() {
            public string $foo = 'foo';
        };

        $to = new class() {
            public string $foo;
        };

        $mappedObject = $this->getMapper()->map($from, \get_class($to));

        $this->assertEquals('foo', $mappedObject->foo);
    }

    public function testBasicPropertyMappingFromArrayToClass(): void
    {
        $from = [ 'foo' => 'foo'];

        $to = new class() {
            public string $foo;
        };

        $mappedObject = $this->getMapper()->map($from, $to);

        $this->assertEquals('foo', $mappedObject->foo);
    }

    public function testMixedToType(): void
    {
        $from = new class() {
            public $foo = 'foo';
        };

        $to = new class() {
            public string $foo;
        };

        $mappedObject = $this->getMapper()->map($from, $to);

        $this->assertEquals('foo', $mappedObject->foo);
    }

    public function unionTypeTest(): void
    {
        $from = new class() {
            public string|int $foo = 'foo';
        };

        $to = new class() {
            public ?string $foo;
        };

        $mappedObject = $this->getMapper()->map($from, $to);

        $this->assertEquals('foo', $mappedObject->foo);
    }

    public function testIncompatibleTypes(): void
    {
        $from = new class() {
            public string $foo = 'foo';
        };

        $to = new class() {
            public int $foo;
        };

        $this->expectException(PropertyMapException::class);
        $this->expectExceptionMessageMatches(
            '/Cannot map property \"foo\" of \"(.*)\" to destination object of \"(.*)\". Check if writable\/callable and correct type\(s\) \(\[string\] to \[int\]\)/'
        );

        $this->getMapper()->map($from, $to);
    }
}