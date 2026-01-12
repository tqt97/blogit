<?php

declare(strict_types=1);

namespace Modules\Post\Presentation\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Post\Domain\Entities\Post;

class PostPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, ?Post $model = null): bool
    {
        return true;
    }

    public function delete(User $user, ?Post $model = null): bool
    {
        return true;
    }
}
