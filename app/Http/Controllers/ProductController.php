<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;

class ProductController extends Controller
{
    public function index(): \Inertia\Response|\Inertia\ResponseFactory
    {
        $products = Product::all(['id', 'name', 'price']);

        return inertia('Products/Index', compact('products'));
    }

    public function create(): \Inertia\Response|\Inertia\ResponseFactory
    {
        return inertia('Products/Edit');
    }

    public function store(ProductRequest $request): RedirectResponse
    {
        Product::create($request->validated());

        return to_route('products.index');
    }

    public function edit(Product $product): \Inertia\Response|\Inertia\ResponseFactory
    {
        return inertia('Products/Edit', compact('product'));
    }

    public function update(Product $product, ProductRequest $request): RedirectResponse
    {
        $product->update($request->validated());

        return to_route('products.index');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return redirect()->back();
    }

    public function trash(): \Inertia\Response|\Inertia\ResponseFactory
    {
        $products = Product::onlyTrashed()->get();

        return inertia('Products/Trash', compact('products'));
    }

    public function restore(int $id): RedirectResponse
    {
        Product::onlyTrashed()->findOrFail($id)?->restore();

        return redirect()->back();
    }
}
