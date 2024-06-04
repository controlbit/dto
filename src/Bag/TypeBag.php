<?php
declare(strict_types=1);

namespace ControlBit\Dto\Bag;

final readonly class TypeBag implements \Stringable
{
    /**
     * @param  array<string|null>  $types
     */
    public function __construct(private array $types)
    {
    }

    public function has(string $type): bool
    {
        return \in_array($type, $this->types, true);
    }

    public function hasNull(): bool
    {
        return \in_array(null, $this->types, true);
    }

    public function hasIterable(): bool
    {
        return
            \in_array('array', $this->types, true)
            ||
            \in_array('iterable', $this->types, true);
    }

    public function hasInstanceOf(string $classOrInterface): bool
    {
        foreach ($this->types as $type) {
            if (\is_subclass_of($type, $classOrInterface)) { // @phpstan-ignore-line
                return true;
            }
        }

        return false;
    }

    public function getInstanceOf(string $classOrInterface): ?string
    {
        foreach ($this->types as $type) {
            if (\is_subclass_of($type, $classOrInterface)) { // @phpstan-ignore-line
                return $type;
            }
        }

        return null;
    }

    /**
     * @param  class-string|null  $class
     */
    public function hasOnlyOneClass(?string $class = null): bool
    {
        $classes = \array_filter(
            $this->types,
            static fn(?string $type): bool => null !== $type && \class_exists($type),
        );

        if (\count($classes) !== 1) {
            return false;
        }

        if (null === $class) {
            return true;
        }

        return $classes[0] === $class;
    }

    /**
     * @return class-string<object>|null
     */
    public function getOneClass(): ?string
    {
        $classes = \array_filter(
            $this->types,
            static fn(?string $type): bool => null !== $type && \class_exists($type),
        );

        return $classes[0] ?? null;
    }

    /**
     * @return array<string|null>
     */
    public function all(): array
    {
        return $this->types;
    }

    public function __toString(): string
    {
        return \implode(
            '|',
            \array_map(function (string|null $type){
                return $type ?? 'null';
            }, $this->types)
        );
    }
}