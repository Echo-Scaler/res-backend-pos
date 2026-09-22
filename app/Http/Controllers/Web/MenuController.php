<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Menu\StoreCategoryRequest;
use App\Http\Requests\Admin\Menu\StoreProductRequest;
use App\Http\Requests\Admin\Menu\UpdateProductRequest;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ProductResource;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class MenuController extends Controller
{
    /**
     * Display menu products and categories workspace.
     */
    public function index(Request $request): View
    {
        $currentUser = $request->user();
        $restaurant = $currentUser->restaurant;

        $categories = Category::query()
            ->where('restaurant_id', $currentUser->restaurant_id)
            ->withCount('products')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $query = Product::query()
            ->where('restaurant_id', $currentUser->restaurant_id)
            ->with('category');

        if ($categoryId = $request->input('category_id')) {
            $query->where('category_id', $categoryId);
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $products = $query->latest()->paginate(12)->withQueryString();

        $stats = [
            'total_products' => Product::where('restaurant_id', $currentUser->restaurant_id)->count(),
            'available_products' => Product::where('restaurant_id', $currentUser->restaurant_id)->where('is_available', true)->count(),
            'out_of_stock' => Product::where('restaurant_id', $currentUser->restaurant_id)->where('is_available', false)->count(),
            'total_categories' => $categories->count(),
        ];

        return view('admin.menu.index', [
            'restaurant' => $restaurant,
            'currentUser' => $currentUser,
            'categories' => $categories,
            'products' => $products,
            'stats' => $stats,
            'selectedCategory' => $categoryId,
            'search' => $search,
        ]);
    }

    /**
     * Store a new category.
     */
    public function storeCategory(StoreCategoryRequest $request): JsonResponse|RedirectResponse
    {
        $currentUser = $request->user();
        $validated = $request->validated();

        $category = Category::create([
            'restaurant_id' => $currentUser->restaurant_id,
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']).'-'.Str::random(4),
            'description' => $validated['description'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        $message = "Menu category '{$category->name}' created successfully.";

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'category' => new CategoryResource($category),
            ]);
        }

        return redirect()->route('admin.menu.index')->with('success', $message);
    }

    /**
     * Store a new product / dish.
     */
    public function storeProduct(StoreProductRequest $request): JsonResponse|RedirectResponse
    {
        $currentUser = $request->user();
        $validated = $request->validated();

        $product = Product::create([
            'restaurant_id' => $currentUser->restaurant_id,
            'category_id' => $validated['category_id'] ?? null,
            'name' => $validated['name'],
            'code' => $validated['code'] ?? null,
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'cost_price' => $validated['cost_price'] ?? 0,
            'preparation_time' => $validated['preparation_time'] ?? null,
            'is_available' => $validated['is_available'] ?? true,
            'image_url' => $validated['image_url'] ?? null,
        ]);

        $message = "Menu product '{$product->name}' successfully added.";

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'product' => new ProductResource($product->load('category')),
            ]);
        }

        return redirect()->route('admin.menu.index')->with('success', $message);
    }

    /**
     * Update an existing product.
     */
    public function updateProduct(UpdateProductRequest $request, Product $product): JsonResponse|RedirectResponse
    {
        $currentUser = $request->user();

        if ($product->restaurant_id !== $currentUser->restaurant_id) {
            abort(403, 'Unauthorized access to another restaurant product.');
        }

        $validated = $request->validated();
        $product->update($validated);

        $message = "Product '{$product->name}' updated successfully.";

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'product' => new ProductResource($product->load('category')),
            ]);
        }

        return redirect()->route('admin.menu.index')->with('success', $message);
    }

    /**
     * Toggle product daily availability / 86 out of stock status.
     */
    public function toggleProductAvailability(Request $request, Product $product): JsonResponse|RedirectResponse
    {
        $currentUser = $request->user();

        if ($product->restaurant_id !== $currentUser->restaurant_id) {
            abort(403, 'Unauthorized access.');
        }

        $product->update([
            'is_available' => ! $product->is_available,
        ]);

        $statusText = $product->is_available ? 'Available' : 'Out of Stock (86)';
        $message = "{$product->name} status changed to {$statusText}.";

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'is_available' => $product->is_available,
                'product' => new ProductResource($product->load('category')),
            ]);
        }

        return back()->with('success', $message);
    }

    /**
     * Remove the product.
     */
    public function destroyProduct(Request $request, Product $product): RedirectResponse
    {
        $currentUser = $request->user();

        if ($product->restaurant_id !== $currentUser->restaurant_id) {
            abort(403, 'Unauthorized access.');
        }

        $productName = $product->name;
        $product->delete();

        return redirect()->route('admin.menu.index')->with('success', "Product '{$productName}' removed from menu.");
    }
}
