<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query();

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }
        if ($request->filled('price_min')) {
            $query->where('price', '>=', $request->price_min);
        }
        if ($request->filled('price_max')) {
            $query->where('price', '<=', $request->price_max);
        }

        if ($request->filled('sort')) {
            $sorts = explode(',', $request->sort);
            foreach ($sorts as $s) {
                $direction = 'asc';
                if (str_starts_with($s, '-')) {
                    $direction = 'desc';
                    $s = ltrim($s, '-');
                }
                $query->orderBy($s, $direction);
            }
        }

        $perPage = $request->get('per_page', 15);
        $page = $query->paginate($perPage)->appends($request->query());
        return ProductResource::collection($page);
    }

    public function show(Product $product)
    {
        return new ProductResource($product);
    }

    public function store(ProductRequest $req)
    {
        $product = Product::create($req->validated());
        return (new ProductResource($product))->response()->setStatusCode(201);
    }

    public function update(ProductRequest $req, Product $product)
    {
        $product->update($req->validated());
        return new ProductResource($product);
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return response()->json(null, 204);
    }
}
