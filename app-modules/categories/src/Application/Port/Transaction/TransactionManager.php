<?php

declare(strict_types=1);

namespace Modules\Categories\Application\Port\Transaction;

interface TransactionManager
{
    /** @template T */
    public function withinTransaction(callable $fn): mixed;
}
