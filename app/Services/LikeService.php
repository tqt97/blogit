<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class LikeService
{
    public function like(Model $likeable, int $userId): bool
    {
        return DB::transaction(function () use ($likeable, $userId): bool {
            $type = $likeable->getMorphClass();
            $id = $likeable->getKey();

            try {
                DB::table('likes')->insert([
                    'user_id' => $userId,
                    'likeable_type' => $type,
                    'likeable_id' => $id,
                    'created_at' => now(),
                ]);
            } catch (QueryException $e) {
                if ($this->isDuplicateKey($e)) {
                    return false;
                }
                throw $e;
            }

            $likeable
                ->newQuery()
                ->whereKey($id)
                ->increment('likes_count', 1);

            return true;
        }, 3);
    }

    public function unlike(Model $likeable, int $userId): bool
    {
        return DB::transaction(function () use ($likeable, $userId): bool {
            $type = $likeable->getMorphClass();
            $id = $likeable->getKey();

            $deleted = DB::table('likes')
                ->where('user_id', $userId)
                ->where('likeable_type', $type)
                ->where('likeable_id', $id)
                ->delete();

            if ($deleted <= 0) {
                return false;
            }

            $likeable
                ->newQuery()
                ->whereKey($id)
                ->where('likes_count', '>', 0)
                ->decrement('likes_count', 1);

            return true;
        }, 3);
    }

    private function isDuplicateKey(QueryException $e): bool
    {
        // MySQL duplicate entry: SQLSTATE 23000 / error code 1062
        $sqlState = $e->errorInfo[0] ?? null;
        $errCode = $e->errorInfo[1] ?? null;

        return $sqlState === '23000' && (int) $errCode === 1062;
    }
}
