<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(){
        if (Auth::user()->role ==='vendor') {
            $products = Product::where('vendor_id', Auth::id())->get();
        } else {
            $products = Product::all();
        }
        return response()->json($products);
    }

    public function store(Request $request){
        if (Auth::user()->role !== 'vendor') {
            return response()->json(['message' => 'Unauthorized: Only vendors can add products.'], 403);
        }
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $imagePath = null;
        if($request->hasFile('image')){
            $imagePath = $request->file('image')->store('products', 'public');
        }

        $product = Product::create([
            'vendor_id' => Auth::id(),
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock,
            'image' => $imagePath,
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Product added successfully!', 
            'product' => $product,
        ], 201);
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
