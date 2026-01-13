<?php

namespace Modules\Categories\Presentation\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Categories\Application\CommandHandlers\CreateCategoryHandler;
use Modules\Categories\Application\Queries\ListCategoriesQuery;
use Modules\Categories\Application\QueryHandlers\ListCategoriesHandler;
use Modules\Categories\Domain\Exceptions\SlugAlreadyExistsException;
use Modules\Categories\Presentation\Http\Requests\CreateCategoryRequest;
use Modules\Categories\Presentation\Http\Requests\ListCategoriesRequest;
use Modules\Categories\Presentation\Mappers\CreateCategoryCommandMapper;
use Str;

final class CategoryController
{
    /**
     * Display a paginated and searchable list of categories.
     */
    public function index(ListCategoriesRequest $request, ListCategoriesHandler $handler): Response
    {

        $validatedData = $request->validated();

        $query = new ListCategoriesQuery(
            search: $validatedData['search'] ?? null,
            perPage: (int) ($validatedData['perPage'] ?? 5),
            sort: $validatedData['sort'] ?? 'id',
            direction: $validatedData['direction'] ?? 'desc'
        );

        $categories = $handler->handle($query);

        return Inertia::render('categories/index', [
            'categories' => $categories,
            'filters' => $validatedData,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateCategoryRequest $request, CreateCategoryCommandMapper $mapper, CreateCategoryHandler $handler): RedirectResponse
    {

        $data = $request->validated();

        try {
            $handler->handle($mapper($data));

            // if ($data['intent'] === Intent::CreateAndContinue->value) {
            //     return redirect()->route('tags.create')->with($this->flash('Tag created. Continue creating tags.'));
            // }

            return redirect()->route('categories.index')->with($this->flash('Category created.', 'message'));
        } catch (SlugAlreadyExistsException) {
            throw ValidationException::withMessages(['slug' => 'Slug already exists.']);
        }
    }

    private function flash(string $message, string $type = 'success'): array
    {
        return [$type => $message, 'flash_id' => (string) Str::uuid()];
    }
}
