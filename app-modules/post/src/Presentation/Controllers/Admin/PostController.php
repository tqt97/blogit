<?php

declare(strict_types=1);

namespace Modules\Post\Presentation\Controllers\Admin;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Post\Application\CommandHandlers\BulkDeletePostsHandler;
use Modules\Post\Application\CommandHandlers\CreatePostHandler;
use Modules\Post\Application\CommandHandlers\DeletePostHandler;
use Modules\Post\Application\CommandHandlers\UpdatePostHandler;
use Modules\Post\Application\Commands\BulkDeletePostsCommand;
use Modules\Post\Application\Commands\DeletePostCommand;
use Modules\Post\Application\Queries\ShowPostQuery;
use Modules\Post\Application\QueryHandlers\ListPostsHandler;
use Modules\Post\Application\QueryHandlers\ShowPostHandler;
use Modules\Post\Domain\Entities\Post;
use Modules\Post\Domain\Exceptions\PostInUseException;
use Modules\Post\Domain\Exceptions\PostNotFoundException;
use Modules\Post\Domain\Exceptions\SlugAlreadyExistsException;
use Modules\Post\Domain\ValueObjects\Intent;
use Modules\Post\Domain\ValueObjects\PostIds;
use Modules\Post\Presentation\Mappers\CreatePostCommandMapper;
use Modules\Post\Presentation\Mappers\ListPostsQueryMapper;
use Modules\Post\Presentation\Mappers\UpdatePostCommandMapper;
use Modules\Post\Presentation\Requests\BulkDestroyPostRequest;
use Modules\Post\Presentation\Requests\ListPostsRequest;
use Modules\Post\Presentation\Requests\StorePostRequest;
use Modules\Post\Presentation\Requests\UpdatePostRequest;

final class PostController
{
    public function index(ListPostsRequest $request, ListPostsQueryMapper $mapper, ListPostsHandler $handler): Response
    {
        Gate::authorize('viewAny', Post::class);

        $filters = $request->filters();

        return Inertia::render('admin/posts/index', [
            'posts' => $handler->handle($mapper($filters)),
            'filters' => $filters,
        ]);
    }

    public function create(): Response
    {
        Gate::authorize('create', Post::class);

        return Inertia::render('admin/posts/create');
    }

    public function store(StorePostRequest $request, CreatePostCommandMapper $mapper, CreatePostHandler $handler): RedirectResponse
    {
        Gate::authorize('create', Post::class);

        $data = $request->validated();

        try {
            $handler->handle($mapper($data));

            if (($data['intent'] ?? '') === Intent::CreateAndContinue->value) {
                return redirect()->route('posts.create')->with($this->flash('Post created. Continue creating posts.'));
            }

            return redirect()->route('posts.index')->with($this->flash('Post created.'));
        } catch (SlugAlreadyExistsException) {
            throw ValidationException::withMessages(['slug' => 'Slug already exists.']);
        }
    }

    public function edit(int $id, ShowPostHandler $handler): Response
    {
        Gate::authorize('update', Post::class);

        try {
            $postData = $handler->handle(new ShowPostQuery($id));

            return Inertia::render('admin/posts/edit', [
                'post' => $postData,
            ]);
        } catch (PostNotFoundException) {
            abort(404);
        }
    }

    public function update(int $id, UpdatePostRequest $request, UpdatePostCommandMapper $mapper, UpdatePostHandler $handler): RedirectResponse
    {
        Gate::authorize('update', Post::class);

        try {
            $handler->handle($mapper($id, $request->validated()));

            return back()->with($this->flash('Post updated.'));
        } catch (PostNotFoundException) {
            abort(404);
        } catch (SlugAlreadyExistsException) {
            throw ValidationException::withMessages(['slug' => 'Slug already exists.']);
        }
    }

    public function destroy(int $id, DeletePostHandler $handler): RedirectResponse
    {
        Gate::authorize('delete', Post::class);

        try {
            $handler->handle(new DeletePostCommand($id));

            return back()->with($this->flash('Post deleted.'));
        } catch (PostNotFoundException) {
            abort(404);
        } catch (PostInUseException) {
            return back()->with($this->flash('Cannot delete post. It may be in use.', 'error'));
        }
    }

    public function bulkDestroy(BulkDestroyPostRequest $request, BulkDeletePostsHandler $handler): RedirectResponse
    {
        Gate::authorize('delete', Post::class);

        try {
            $handler->handle(new BulkDeletePostsCommand(new PostIds($request->validated('ids'))));

            return back()->with($this->flash('Selected posts deleted.'));
        } catch (PostInUseException) {
            return back()->with($this->flash('One or more posts could not be deleted. They may be in use.', 'error'));
        }
    }

    private function flash(string $message, string $type = 'success'): array
    {
        return [$type => $message, 'flash_id' => (string) Str::uuid()];
    }
}
