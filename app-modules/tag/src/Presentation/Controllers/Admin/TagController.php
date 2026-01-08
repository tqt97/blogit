<?php

namespace Modules\Tag\Presentation\Controllers\Admin;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
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
use Modules\Tag\Domain\ValueObjects\Intent;
use Modules\Tag\Presentation\Mappers\CreateTagCommandMapper;
use Modules\Tag\Presentation\Mappers\ListTagsQueryMapper;
use Modules\Tag\Presentation\Mappers\UpdateTagCommandMapper;
use Modules\Tag\Presentation\Requests\BulkDestroyTagRequest;
use Modules\Tag\Presentation\Requests\ListTagsRequest;
use Modules\Tag\Presentation\Requests\StoreTagRequest;
use Modules\Tag\Presentation\Requests\UpdateTagRequest;

class TagController
{
    public function index(ListTagsRequest $request, ListTagsQueryMapper $mapper, ListTagsHandler $handler): Response
    {
        $filters = $request->filters();

        return Inertia::render('admin/tags/index', [
            'tags' => $handler->handle($mapper($filters)),
            'filters' => $filters,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/tags/create');
    }

    public function store(StoreTagRequest $request, CreateTagCommandMapper $mapper, CreateTagHandler $handler): RedirectResponse
    {
        $handler->handle($mapper($request->validated()));

        if ($request->validated('intent') == Intent::CreateAndContinue->value) {
            return redirect()->route('tags.create')->with($this->flash('Tag created. Continue creating tags.'));
        }

        return redirect()->route('tags.index')->with($this->flash('Tag created.'));
    }

    public function edit(int $tag, ShowTagHandler $handler): Response
    {
        $tagData = $handler->handle(new ShowTagQuery($tag));
        abort_if(! $tagData, 404);

        return Inertia::render('admin/tags/edit', [
            'tag' => $tagData,
        ]);
    }

    public function update(int $tag, UpdateTagRequest $request, UpdateTagCommandMapper $mapper, UpdateTagHandler $handler): RedirectResponse
    {
        $handler->handle($mapper($tag, $request->validated()));

        return back()->with($this->flash('Tag updated.'));
    }

    public function destroy(int $tag, DeleteTagHandler $handler): RedirectResponse
    {
        $handler->handle(new DeleteTagCommand($tag));

        return back()->with($this->flash('Tag deleted.'));
    }

    public function bulkDestroy(BulkDestroyTagRequest $request, BulkDeleteTagsHandler $handler): RedirectResponse
    {
        $handler->handle(new BulkDeleteTagsCommand($request->validated()));

        return back()->with($this->flash('Selected tags deleted.'));
    }

    private function flash(string $message, string $type = 'success'): array
    {
        return [$type => $message, 'flash_id' => (string) Str::uuid()];
    }
}
