<?php

declare(strict_types=1);

namespace Modules\Tag\Application\Ports\Transaction;

interface TransactionManager
{
    /** @template T */
    public function withinTransaction(callable $fn): mixed;
}
