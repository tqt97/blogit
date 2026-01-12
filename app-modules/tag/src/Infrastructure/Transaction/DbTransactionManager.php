<?php

declare(strict_types=1);

namespace Modules\Tag\Infrastructure\Transaction;

use Illuminate\Support\Facades\DB;
use Modules\Tag\Application\Ports\Transaction\TransactionManager;

final class DbTransactionManager implements TransactionManager
{
    public function withinTransaction(callable $fn): mixed
    {
        return DB::transaction($fn);
    }
}
