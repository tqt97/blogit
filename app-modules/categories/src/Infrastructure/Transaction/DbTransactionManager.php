<?php

declare(strict_types=1);

namespace Modules\Categories\Infrastructure\Transaction;

use Illuminate\Support\Facades\DB;
use Modules\Categories\Application\Port\Transaction\TransactionManager;

final class DbTransactionManager implements TransactionManager
{
    public function withinTransaction(callable $fn): mixed
    {
        return DB::transaction($fn);
    }
}
