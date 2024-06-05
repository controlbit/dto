<?php
declare(strict_types=1);

namespace ControlBit\Dto\Tests\Mapper\ViaSetter;

use ControlBit\Dto\Attribute\MapTo;
use ControlBit\Dto\Exception\PropertyMapException;
use ControlBit\Dto\Tests\LibraryTestCase;

class TypedTest extends LibraryTestCase
{
    public function testViaSetter(): void
    {
        $from = new class() {
            #[Mapto('setBar')]
            public string $foo = 'foo';
        };

        $to = new class() {
            private string $bar;

            public function setBar(string $foo)
            {
                $this->bar = $foo;
            }

            public function getFoo()
            {
                return $this->bar;
            }
        };

        $mappedObject = $this->getMapper()->map($from, $to);

        $this->assertEquals('foo', $mappedObject->getFoo());
    }

    public function testViaSetterWrongType(): void
    {
        $from = new class() {
            #[MapTo('setBar')]
            public string $foo = 'foo';
        };

        $to = new class() {
            private bool $bar = false;

            public function setBar(bool $foo)
            {
                $this->bar = $foo;
            }

            public function isBar(): bool
            {
                return $this->bar;
            }
        };

        $this->expectException(PropertyMapException::class);
        $this->expectExceptionMessageMatches(
            '/Cannot map property "foo" of "(.*)" to destination object of "(.*)"\. Check if writable\/callable and correct type\(s\)/'
        );

        $this->getMapper()->map($from, $to);
    }

    public function testSetterMethodDoesNotExistsLeavesUnmapped(): void
    {
        $from = new class() {
            #[MapTo('setBar')]
            public string $foo = 'foo';
        };

        $to = new class() {
            private bool $bar = false;

            public function isBar(): bool
            {
                return $this->bar;
            }
        };

        $mapped = $this->getMapper()->map($from, $to);

        self::assertFalse($mapped->isBar());
    }

    public function testSetterNonPublicMethodThrowsException(): void
    {
        $from = new class() {
            #[MapTo('setBar')]
            public string $foo = 'foo';
        };

        $to = new class() {
            private string $bar;

            private function setBar(string $foo)
            {
                $this->bar = $foo;
            }
        };

        $this->expectException(PropertyMapException::class);
        $this->getMapper()->map($from, $to);
    }
}

