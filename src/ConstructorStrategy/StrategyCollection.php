<?php
declare(strict_types=1);
namespace ControlBit\Dto\ConstructorStrategy;

use ControlBit\Dto\Contract\ConstructorStrategyInterface;
use ControlBit\Dto\Enum\ConstructorStrategy;
use ControlBit\Dto\Exception\LogicException;
use Traversable;

/**
 * @implements \IteratorAggregate<ConstructorStrategyInterface>
 */
readonly class StrategyCollection implements \IteratorAggregate
{
    /**
     * @param  iterable<ConstructorStrategyInterface>  $strategies
     */
    public function __construct(
        private iterable $strategies,
        private ConstructorStrategy $defaultStrategy,

    )
    {
    }

    public function getStrategy(ConstructorStrategy $strategy): ConstructorStrategyInterface
    {
        foreach ($this as $item) {
            if ($strategy->value === $item->getName()) {
                return $item;
            }
        }

        throw new LogicException('Strategy not found.');
    }

    public function getDefaultStrategy(): ConstructorStrategyInterface
    {
        return $this->getStrategy($this->defaultStrategy);
    }

    /**
     * @return Traversable<ConstructorStrategyInterface>
     */
    public function getIterator(): \Traversable
    {
        foreach ($this->strategies as $strategy) {
            yield $strategy;
        }
    }
}