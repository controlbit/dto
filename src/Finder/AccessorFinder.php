<?php
declare(strict_types=1);

namespace ControlBit\Dto\Finder;

use ControlBit\Dto\Accessor\Accessor;
use ControlBit\Dto\Accessor\Getter\MethodGetter;
use ControlBit\Dto\Accessor\Getter\PropertyGetter;
use ControlBit\Dto\Accessor\Setter\MethodSetter;
use ControlBit\Dto\Accessor\Setter\PropertyReflectionSetter;
use ControlBit\Dto\Accessor\Setter\PropertySetter;
use ControlBit\Dto\Bag\AttributeBag;
use ControlBit\Dto\Bag\TypeBag;
use ControlBit\Dto\Contract\Accessor\AccessorInterface;
use ControlBit\Dto\Contract\Accessor\GetterInterface;
use ControlBit\Dto\Contract\Accessor\SetterInterface;
use ControlBit\Dto\Util\TypeTool;
use function ControlBit\Dto\instantiate_attributes;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * TODO: Try to refactor in future.
 */
readonly final class AccessorFinder
{
    public function __construct(private bool $mapPrivateProperties)
    {
    }

    public function find(
        \ReflectionObject   $reflectionObject,
        \ReflectionProperty $reflectionProperty,
    ): AccessorInterface {
        return new Accessor(
            $this->findSetter($reflectionObject, $reflectionProperty),
            $this->findGetter($reflectionObject, $reflectionProperty),
        );
    }

    /**
     * @param  \ReflectionClass<object>  $reflectionClass
     * @param  \ReflectionProperty       $reflectionProperty
     */
    private function findSetter(
        \ReflectionClass    $reflectionClass,
        \ReflectionProperty $reflectionProperty,
    ): ?SetterInterface {

        $methodName = \sprintf('set%s', \ucfirst($reflectionProperty->name));

        if ($reflectionClass->hasMethod($methodName)) {
            $reflectionMethod = $reflectionClass->getMethod($methodName);

            return new MethodSetter(
                $methodName,
                new TypeBag(TypeTool::getReflectionTypes($reflectionMethod)),
                AttributeBag::fromArray(instantiate_attributes($reflectionMethod)),
            );
        }

        $args = [
            $reflectionProperty->name,
            new TypeBag(TypeTool::getReflectionTypes($reflectionProperty)),
            AttributeBag::fromArray(instantiate_attributes($reflectionProperty)),
        ];

        if ($reflectionProperty->isPublic()) {
            return new PropertySetter(...$args);
        }

        if ($this->mapPrivateProperties) {
            return new PropertyReflectionSetter(...$args);
        }

        return null;
    }

    /**
     * @param  \ReflectionClass<object>  $reflectionClass
     */
    private function findGetter(
        \ReflectionClass    $reflectionClass,
        \ReflectionProperty $reflectionProperty,
    ): ?GetterInterface {
        if ($reflectionProperty->isPublic()) {
            return new PropertyGetter(
                $reflectionProperty->name,
                AttributeBag::fromArray(instantiate_attributes($reflectionProperty))
            );
        }

        foreach ($this->generatePossibleGetterMethodNames($reflectionProperty->name) as $methodName) {
            if (!$reflectionClass->hasMethod($methodName)) {
                continue;
            }

            return new MethodGetter(
                $methodName,
                AttributeBag::fromArray(instantiate_attributes($reflectionClass->getMethod($methodName)),
                )
            );
        }

        return null;
    }

    /**
     * @return string[]
     */
    private function generatePossibleGetterMethodNames(string $propName): array
    {
        return [
            \sprintf('get%s', \ucfirst($propName)),
            \sprintf('is%s', \ucfirst($propName)),
            \sprintf('has%s', \ucfirst($propName)),
            \sprintf('have%s', \ucfirst($propName)),
        ];
    }
}