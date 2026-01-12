<?php

declare(strict_types=1);

namespace Modules\Post\Infrastructure\Transaction;

use Illuminate\Support\Facades\DB;
use Modules\Post\Application\Ports\Transaction\TransactionManager;

final class DbTransactionManager implements TransactionManager
{
    public function withinTransaction(callable $fn): mixed
    {
        return DB::transaction($fn);
    }
}
