<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResources;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        return ProductResources::collection(Product::query()->paginate(10));
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'price' => 'required | numeric',
            'category' => 'required',
        ]);


        $product = Product::create($request->all());
        return response()->json($product, 201);
    }

    public function filter(Request $request)
    {
        $query = Product::query();
        if ($request->has('category')) {
            $query->where('category', $request->category);
        }
        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }
        $filteredProducts = $query->paginate();
        return response()->json($filteredProducts);
    }

}
