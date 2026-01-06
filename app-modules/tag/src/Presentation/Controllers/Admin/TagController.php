<?php

namespace Modules\Tag\Presentation\Controllers\Admin;

use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Tag\Application\CommandHandlers\BulkDeleteTagsHandler;
use Modules\Tag\Application\CommandHandlers\CreateTagHandler;
use Modules\Tag\Application\CommandHandlers\DeleteTagHandler;
use Modules\Tag\Application\CommandHandlers\UpdateTagHandler;
use Modules\Tag\Application\Commands\BulkDeleteTagsCommand;
use Modules\Tag\Application\Commands\CreateTagCommand;
use Modules\Tag\Application\Commands\DeleteTagCommand;
use Modules\Tag\Application\Commands\UpdateTagCommand;
use Modules\Tag\Application\Queries\ListTagsQuery;
use Modules\Tag\Application\Queries\ShowTagQuery;
use Modules\Tag\Application\QueryHandlers\ListTagsHandler;
use Modules\Tag\Application\QueryHandlers\ShowTagHandler;
use Modules\Tag\Presentation\Requests\BulkDestroyTagRequest;
use Modules\Tag\Presentation\Requests\ListTagsRequest;
use Modules\Tag\Presentation\Requests\StoreTagRequest;
use Modules\Tag\Presentation\Requests\UpdateTagRequest;

class TagController
{
    public function index(ListTagsRequest $request, ListTagsHandler $handler): Response
    {
        $filters = $request->filters();

        $tags = $handler->handle(new ListTagsQuery(
            search: $filters['search'],
            page: $filters['page'],
            perPage: $filters['per_page'],
            sort: $filters['sort'],
            direction: $filters['direction'],
        ));

        return Inertia::render('admin/tags/index', [
            'tags' => $tags,
            'filters' => $filters,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/tags/create');
    }

    public function store(StoreTagRequest $request, CreateTagHandler $handler): RedirectResponse
    {
        $data = $request->validated();

        $handler->handle(new CreateTagCommand(
            name: (string) $data['name'],
            slug: (string) $data['slug'],
        ));

        $intent = $data['intent'] ?? 'default';

        if ($intent === 'create_and_continue') {
            return redirect()
                ->route('tags.create')
                ->with('success', 'Tag created. Add another.')
                ->with('flash_id', (string) str()->uuid());
        }

        return redirect()
            ->route('tags.index')
            ->with('success', 'Tag created.')
            ->with('flash_id', (string) str()->uuid());
    }

    public function edit(int $tag, ShowTagHandler $handler): Response
    {
        $tagData = $handler->handle(new ShowTagQuery($tag));
        abort_if(! $tagData, 404);

        return Inertia::render('admin/tags/edit', [
            'tag' => $tagData,
        ]);
    }

    public function update(int $id, UpdateTagRequest $request, UpdateTagHandler $handler): RedirectResponse
    {
        $data = $request->validated();

        $handler->handle(new UpdateTagCommand(
            id: $id,
            name: (string) $data['name'],
            slug: (string) $data['slug'],
        ));

        return back()->with('success', 'Tag updated.')->with('flash_id', (string) str()->uuid());
    }

    public function destroy(int $tag, DeleteTagHandler $handler): RedirectResponse
    {
        $handler->handle(new DeleteTagCommand($tag));

        return back()->with('success', 'Tag deleted.')->with('flash_id', (string) str()->uuid());
    }

    public function bulkDestroy(BulkDestroyTagRequest $request, BulkDeleteTagsHandler $handler): RedirectResponse
    {
        $handler->handle(new BulkDeleteTagsCommand($request->ids()));

        return back()->with('success', 'Selected tags deleted.')->with('flash_id', (string) str()->uuid());
    }
}
