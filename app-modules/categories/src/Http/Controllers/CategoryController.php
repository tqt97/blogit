<?php

namespace Modules\Categories\Http\Controllers;

use Modules\Categories\Constants\Category;
use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Illuminate\Http\RedirectResponse;
use Inertia\Response;
use Modules\Categories\Http\Requests\CategoryRequest;
use Modules\Categories\Services\CategoryService;

class CategoryController extends Controller
{
    protected $categoryService;
    /**
     * Inject Category repository
     *
     * @param CategoryService $categoryService
     */
    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    /**
     * Get all categories with full subcategories
     *
     */
    public function index(): Response
    {
        $categories = $this->categoryService->getAll(Category::GET_ROOT);
        return Inertia::render('categories/index', [
            'categories' => $categories
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CategoryRequest $request
     * @return RedirectResponse
     */
    public function store(CategoryRequest $request): RedirectResponse
    {
        try {
            $this->categoryService->store($request->validated());
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
        return to_route('categories.index');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param CategoryRequest $request
     * @param int $id
     * @return RedirectResponse
     */
    public function update(CategoryRequest $request, int $id): RedirectResponse
    {
        try {
            $this->categoryService->update($id, $request->validated());
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
        return to_route('categories.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy(int $id): RedirectResponse
    {
        try {
            $this->categoryService->delete($id);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
        return to_route('categories.index');
    }
}