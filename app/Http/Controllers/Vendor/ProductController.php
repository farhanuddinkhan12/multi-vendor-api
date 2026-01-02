<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
{
    $user = Auth::user();

    if ($user) {
        if ($user->role === 'vendor') {
            $products = Product::where('vendor_id', $user->id)->get();
        } elseif ($user->role === 'admin') {
            $products = Product::all();
        } else {
            $products = Product::where('status', 'approved')->get();
        }
    } else {
        // Guest user
        $products = Product::where('status', 'approved')->get();
    }


    return response()->json($products);
}

    public function store(Request $request)
    {
        if (Auth::user()->role !== 'vendor') {
            return response()->json(['message' => 'Unauthorized: Only vendors can add products.'], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_featured' => 'boolean',
        ]);

        $imagePath = $request->hasFile('image') 
            ? $request->file('image')->store('products', 'public') 
            : null;

        $product = Product::create([
            'vendor_id' => Auth::id(),
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock,
            'image' => $imagePath,
            'status' => 'pending',
            'is_featured' => $request->input('is_featured', false),
        ]);

        return response()->json([
            'message' => 'Product added successfully!',
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'price' => $product->price,
                'stock' => $product->stock,
                'image_url' => $product->image ? asset('storage/' . $product->image) : null,
                'status' => $product->status,
                'is_featured' => $product->is_featured,
            ],
        ], 201);
    }

    public function featuredProducts()
    {
        $products = Product::where('is_featured', true)
            ->with('vendor:id,name') // Eager load vendor
            ->latest()
            ->get();

        return response()->json($products);
    }




    public function update(Request $request, Product $product){
        if($product->vendor_id !== Auth::id()){
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if($request->hasFile('image')){
            if($product->image){
                Storage::disk('public')->delete($product->image);
            }

            $product->image = $request->file('image')->store('products', 'public');
        }

        $product->update($request->only('name', 'description', 'price', 'stock'));
        return response()->json([
            'message' => 'Product updated successfully!',
            'product' => $product
        ]);
    }

    public function destroy(Product $product){
        if($product->vendor_id !== Auth::id()){
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
    }
}
