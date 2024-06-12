<?php
declare(strict_types=1);

namespace ControlBit\Dto\ConstructorStrategy;

use ControlBit\Dto\Accessor\Setter\ConstructorSetter;
use ControlBit\Dto\Bag\AttributeBag;
use ControlBit\Dto\Bag\TypeBag;
use ControlBit\Dto\Contract\Accessor\GetterInterface;
use ControlBit\Dto\Contract\ConstructorStrategyInterface;
use ControlBit\Dto\Exception\InvalidArgumentException;
use ControlBit\Dto\Exception\MissingArgumentException;
use ControlBit\Dto\Exception\MissingConstructorException;
use ControlBit\Dto\Mapper\Mapper;
use ControlBit\Dto\Mapper\ValueConverter;
use ControlBit\Dto\MetaData\Class\ClassMetadata;
use ControlBit\Dto\MetaData\Map\MapMetadataCollection;
use ControlBit\Dto\Util\TypeTool;
use function ControlBit\Dto\instantiate_attributes;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * TODO: Refactor to decouple.
 */
final readonly class AlwaysStrategy implements ConstructorStrategyInterface
{
    public const NAME = 'always';

    public function __construct(private ValueConverter $valueConverter)
    {
    }

    public function validate(
        \ReflectionClass $destinationReflectionClass,
        MapMetadataCollection $sourceMapMetadataCollection,
    ): void {
        $constructor = $destinationReflectionClass->getConstructor();

        if (null === $constructor) {
            throw new InvalidArgumentException(
                \sprintf('Constructor Strategy is set to Always, but "%s" has no constructor or it\'s private.',
                         $destinationReflectionClass->getName()
                )
            );
        }

        if ($constructor->getNumberOfRequiredParameters() > \count($sourceMapMetadataCollection)) {
            throw new InvalidArgumentException(
                \sprintf(
                    "Not enough members to map as arguments in constructor (Strategy \"%s\".).", $this->getName()
                )
            );
        }
    }

    /**
     * {@inheritDoc}
     */
    public function create(
        Mapper                $mapper,
        object                $source,
        ClassMetadata         $sourceMetadata,
        MapMetadataCollection $sourceMapMetadataCollection,
        \ReflectionClass      $reflectionClass,
    ): object {
        $constructor = $reflectionClass->getConstructor();

        if (null === $constructor) {
            throw new MissingConstructorException(
                \sprintf('Class "%s" is missing constructor.', $reflectionClass->getName())
            );
        }

        $availableArguments = $constructor->getParameters();
        $argumentsToPass    = [];

        foreach ($availableArguments as $argument) {
            $sourceMemberMetadata = $sourceMapMetadataCollection->getHavingDestinationMember($argument->getName());

            if (null === $sourceMemberMetadata) {
                $argumentsToPass[] = $this->getArgumentValue(null, $argument);
                continue;
            }

            $sourceMemberMetadata->setMappedInConstructor();
            $propertyMetadata = $sourceMetadata->getProperty($sourceMemberMetadata->getSourceMember());

            /** @var GetterInterface $getter */
            $getter = $propertyMetadata?->getAccessor()->getGetter();
            $setter = new ConstructorSetter(
                new TypeBag(TypeTool::getReflectionTypes($argument)),
                AttributeBag::fromArray(instantiate_attributes($argument)),
            );

            $value = $this->valueConverter->map(
                $mapper,
                $sourceMetadata,
                $setter,
                $sourceMemberMetadata,
                $getter->get($source),
            );

            $argumentsToPass[] = $this->getArgumentValue($value, $argument);
        }

        return $reflectionClass->newInstanceArgs($argumentsToPass);
    }

    private function getArgumentValue(mixed $value, \ReflectionParameter $argument): mixed
    {
        if ($value !== null) {
            return $value;
        }

        if ($argument->allowsNull()) {
            return null;
        }

        if (!$argument->isDefaultValueAvailable()) {
            throw new MissingArgumentException('Tried to put value via Constructor.');
        }

        return $argument->getDefaultValue();
    }

    public function getName(): string
    {
        return self::NAME;
    }
}