<?php

declare(strict_types=1);

namespace Modules\Tag\Domain\ValueObjects;

use InvalidArgumentException;

final readonly class TagIds
{
    /**
     * @var TagId[]
     */
    private array $values;

    /**
     * @param  int[]  $ids
     */
    public function __construct(array $ids)
    {
        if (empty($ids)) {
            throw new InvalidArgumentException('Ids array cannot be empty.');
        }

        $uniqueIds = array_values(array_unique(array_map('intval', $ids)));
        $positiveIds = array_filter($uniqueIds, fn (int $id) => $id > 0);

        if (empty($positiveIds)) {
            throw new InvalidArgumentException('No valid positive integer IDs provided.');
        }

        $this->values = array_map(fn (int $id) => new TagId($id), $positiveIds);
    }

    /**
     * @return TagId[]
     */
    public function all(): array
    {
        return $this->values;
    }

    /**
     * @return int[]
     */
    public function toScalars(): array
    {
        return array_map(fn (TagId $id) => $id->value(), $this->values);
    }
}
