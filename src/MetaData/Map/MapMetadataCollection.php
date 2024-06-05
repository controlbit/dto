<?php
declare(strict_types=1);

namespace ControlBit\Dto\MetaData\Map;

/**
 * @implements \IteratorAggregate<MemberMapMetadata>
 */
final class MapMetadataCollection implements \IteratorAggregate, \Countable
{
    /**
     * @var MemberMapMetadata[]
     */
    private array $collection = [];

    public function add(MemberMapMetadata $mapMetadata): void
    {
        $this->collection[] = $mapMetadata;
    }

    /**
     * @return MemberMapMetadata[]
     */
    public function getAll(): array
    {
        return $this->collection;
    }

    public function getHavingDestinationMember(string $member): ?MemberMapMetadata
    {
        foreach ($this->collection as $mapMetadata) {
            if ($mapMetadata->getDestinationMember() === $member) {
                return $mapMetadata;
            }
        }

        return null;
    }

    public function getIterator(): \Traversable
    {
        yield from $this->collection;
    }

    public function count(): int
    {
        return \count($this->collection);
    }
}