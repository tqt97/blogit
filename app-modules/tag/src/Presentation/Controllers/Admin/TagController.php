<?php

namespace Modules\Tag\Presentation\Controllers\Admin;

use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Tag\Application\CommandHandlers\CreateTagHandler;
use Modules\Tag\Application\CommandHandlers\DeleteTagHandler;
use Modules\Tag\Application\CommandHandlers\UpdateTagHandler;
use Modules\Tag\Application\Commands\CreateTagCommand;
use Modules\Tag\Application\Commands\DeleteTagCommand;
use Modules\Tag\Application\Commands\UpdateTagCommand;
use Modules\Tag\Application\Queries\ListTagsQuery;
use Modules\Tag\Application\Queries\ShowTagQuery;
use Modules\Tag\Application\QueryHandlers\ListTagsHandler;
use Modules\Tag\Application\QueryHandlers\ShowTagHandler;
use Modules\Tag\Presentation\Requests\StoreTagRequest;
use Modules\Tag\Presentation\Requests\UpdateTagRequest;

class TagController
{
    public function index(ListTagsHandler $handler): Response
    {
        $query = new ListTagsQuery(
            search: request('search'),
            perPage: (int) request('per_page', 15),
            sort: (string) request('sort', 'id'),
            direction: (string) request('direction', 'desc'),
        );

        $tags = $handler->handle($query);

        return Inertia::render('admin/tags/index', [
            'tags' => $tags,  // paginator
            'filters' => [
                'search' => request('search'),
                'per_page' => (int) request('per_page', 15),
                'sort' => (string) request('sort', 'id'),
                'direction' => (string) request('direction', 'desc'),
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/tags/create');
    }

    public function store(StoreTagRequest $request, CreateTagHandler $handler): RedirectResponse
    {
        $dto = $handler->handle(new CreateTagCommand(
            name: $request->string('name')->toString(),
            slug: $request->filled('slug') ? $request->string('slug')->toString() : null,
        ));

        return redirect()
            ->route('tags.edit', $dto->id)
            ->with('success', 'Tag created.');
    }

    public function edit(int $tag, ShowTagHandler $handler): Response
    {
        $tagData = $handler->handle(new ShowTagQuery($tag));
        abort_if(!$tagData, 404);

        return Inertia::render('admin/tags/edit', [
            'tag' => $tagData,
        ]);
    }

    public function update(int $tag, UpdateTagRequest $request, UpdateTagHandler $handler): RedirectResponse
    {
        $handler->handle(new UpdateTagCommand(
            id: $tag,
            name: $request->string('name')->toString(),
            slug: $request->filled('slug') ? $request->string('slug')->toString() : null,
        ));

        return back()->with('success', 'Tag updated.');
    }

    public function destroy(int $tag, DeleteTagHandler $handler): RedirectResponse
    {
        $handler->handle(new DeleteTagCommand($tag));

        return redirect()
            ->route('tags.index')
            ->with('success', 'Tag deleted.');
    }
}
