<?php

declare(strict_types=1);

namespace Modules\Tag\Presentation\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Tag\Domain\Entities\Tag;

class TagPolicy
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

    public function update(User $user, ?Tag $tag = null): bool
    {
        return true;
    }

    public function delete(User $user, ?Tag $tag = null): bool
    {
        return true;
    }
}
