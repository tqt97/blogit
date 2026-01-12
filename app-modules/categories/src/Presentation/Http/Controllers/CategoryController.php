<?php

namespace Modules\Categories\Presentation\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Categories\Application\Queries\ListCategoriesQuery;
use Modules\Categories\Application\QueryHandlers\ListCategoriesHandler;
use Modules\Categories\Presentation\Http\Requests\CreateCategoryRequest;
use Modules\Categories\Presentation\Http\Requests\ListCategoriesRequest;

class CategoryController extends Controller
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

    // /**
    //  * Store a newly created resource in storage.
    //  */
    // public function store(CreateCategoryRequest $request): RedirectResponse
    // {
    //     try {
    //         $validatedData = $request->validated();

    //         $this->categoryService->store($request->validated());

    //         return to_route('categories.index')->with('message', 'Category created successfully.');

    //     } catch (Exception $e) {
    //         return redirect()->back()->withErrors(['error' => $e->getMessage()]);
    //     }

    // }
}
