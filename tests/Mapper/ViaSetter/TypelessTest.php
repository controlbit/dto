<?php

namespace ControlBit\Dto\Tests\Mapper\ViaSetter;

use ControlBit\Dto\Attribute\MapTo;
use ControlBit\Dto\Exception\PropertyMapException;
use ControlBit\Dto\Tests\LibraryTestCase;

class TypelessTest extends LibraryTestCase
{
    public function testViaSetter(): void
    {
        $from = new class() {
            #[MapTo('setBar')]
            public $foo = 'foo';
        };

        $to = new class() {
            private $bar;

            public function setBar($foo)
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

    public function testSetterMethodDoesNotExistsLeavesUnmapped(): void
    {
        $from = new class() {
            #[MapTo('setFoo')]
            public $foo = 'foo';
        };

        $to = new class() {
            private $bar = false;

            public function setBar($foo)
            {
                $this->bar = $foo;
            }

            public function getBar()
            {
                return $this->bar;
            }
        };

        $mapped = $this->getMapper()->map($from, $to);
        self::assertFalse($mapped->getBar());
    }

    public function testSetterNonPublicMethodThrowsException(): void
    {
        $from = new class() {
            #[MapTo('setBar')]
            public $foo = 'foo';
        };

        $to = new class() {
            private $bar = false;

            private function setBar($foo)
            {
                $this->bar = $foo;
            }
        };

        $this->expectException(PropertyMapException::class);

        $this->getMapper()->map($from, $to);
    }
}

