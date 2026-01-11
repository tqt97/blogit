<?php

declare(strict_types=1);

namespace Modules\Tag\Presentation\Controllers\Admin;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Tag\Application\CommandHandlers\BulkDeleteTagsHandler;
use Modules\Tag\Application\CommandHandlers\CreateTagHandler;
use Modules\Tag\Application\CommandHandlers\DeleteTagHandler;
use Modules\Tag\Application\CommandHandlers\UpdateTagHandler;
use Modules\Tag\Application\Commands\BulkDeleteTagsCommand;
use Modules\Tag\Application\Commands\DeleteTagCommand;
use Modules\Tag\Application\Queries\ShowTagQuery;
use Modules\Tag\Application\QueryHandlers\ListTagsHandler;
use Modules\Tag\Application\QueryHandlers\ShowTagHandler;
use Modules\Tag\Domain\Entities\Tag;
use Modules\Tag\Domain\Exceptions\SlugAlreadyExistsException;
use Modules\Tag\Domain\Exceptions\TagInUseException;
use Modules\Tag\Domain\Exceptions\TagNotFoundException;
use Modules\Tag\Domain\ValueObjects\Intent;
use Modules\Tag\Domain\ValueObjects\TagIds;
use Modules\Tag\Presentation\Mappers\CreateTagCommandMapper;
use Modules\Tag\Presentation\Mappers\ListTagsQueryMapper;
use Modules\Tag\Presentation\Mappers\UpdateTagCommandMapper;
use Modules\Tag\Presentation\Requests\BulkDestroyTagRequest;
use Modules\Tag\Presentation\Requests\ListTagsRequest;
use Modules\Tag\Presentation\Requests\StoreTagRequest;
use Modules\Tag\Presentation\Requests\UpdateTagRequest;

final class TagController
{
    public function index(ListTagsRequest $request, ListTagsQueryMapper $mapper, ListTagsHandler $handler): Response
    {
        Gate::authorize('viewAny', Tag::class);

        $filters = $request->filters();

        return Inertia::render('admin/tags/index', [
            'tags' => $handler->handle($mapper($filters)),
            'filters' => $filters,
        ]);
    }

    public function create(): Response
    {
        Gate::authorize('create', Tag::class);

        return Inertia::render('admin/tags/create');
    }

    public function store(StoreTagRequest $request, CreateTagCommandMapper $mapper, CreateTagHandler $handler): RedirectResponse
    {
        Gate::authorize('create', Tag::class);

        $data = $request->validated();

        try {
            $handler->handle($mapper($data));

            if ($data['intent'] === Intent::CreateAndContinue->value) {
                return redirect()->route('tags.create')->with($this->flash('Tag created. Continue creating tags.'));
            }

            return redirect()->route('tags.index')->with($this->flash('Tag created.'));
        } catch (SlugAlreadyExistsException) {
            throw ValidationException::withMessages(['slug' => 'Slug already exists.']);
        }
    }

    public function edit(int $id, ShowTagHandler $handler): Response
    {
        Gate::authorize('update', Tag::class);

        try {
            $tagData = $handler->handle(new ShowTagQuery($id));

            return Inertia::render('admin/tags/edit', [
                'tag' => $tagData,
            ]);
        } catch (TagNotFoundException) {
            abort(404);
        }
    }

    public function update(int $id, UpdateTagRequest $request, UpdateTagCommandMapper $mapper, UpdateTagHandler $handler): RedirectResponse
    {
        Gate::authorize('update', Tag::class);

        try {
            $rs = $handler->handle($mapper($id, $request->validated()));

            return back()->with($this->flash($rs->id->value().'Tag updated.'));
        } catch (TagNotFoundException) {
            abort(404);
        } catch (SlugAlreadyExistsException) {
            throw ValidationException::withMessages(['slug' => 'Slug already exists.']);
        }
    }

    public function destroy(int $id, DeleteTagHandler $handler): RedirectResponse
    {
        Gate::authorize('delete', Tag::class);

        try {
            $handler->handle(new DeleteTagCommand($id));

            return back()->with($this->flash('Tag deleted.'));
        } catch (TagNotFoundException) {
            abort(404);
        } catch (TagInUseException) {
            return back()->with($this->flash('Cannot delete tag. It may be in use.', 'error'));
        }
    }

    public function bulkDestroy(BulkDestroyTagRequest $request, BulkDeleteTagsHandler $handler): RedirectResponse
    {
        Gate::authorize('delete', Tag::class);

        try {
            $handler->handle(new BulkDeleteTagsCommand(new TagIds($request->validated('ids'))));

            return back()->with($this->flash('Selected tags deleted.'));
        } catch (TagInUseException) {
            return back()->with($this->flash('One or more tags could not be deleted. They may be in use.', 'error'));
        }
    }

    private function flash(string $message, string $type = 'success'): array
    {
        return [$type => $message, 'flash_id' => (string) Str::uuid()];
    }
}
