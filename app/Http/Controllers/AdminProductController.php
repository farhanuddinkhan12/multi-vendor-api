<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminProductController extends Controller
{
    public function approve($id){
        if(Auth::user()->role !== 'admin'){
            return response()->json(['message' => 'Unauthorized: Only admins can approve products.'], 403);
        }

        $product = Product::findOrFail($id);
        $product->status = 'approved';
        $product->save();

        return response()->json(['message' => 'Product approved successfully!', 'product'=> $product], 201);
    }

    public function reject($id){
        if(Auth::user()->role !== 'admin'){
            return response()->json(['message' => 'Unauthorized: Only admins can reject products'], 403);
        }
        $product = Product::findOrFail($id);
        $product->status = 'rejected';
        $product->save();

        return response()->json(['message'=> 'Product rejected successfully!', 'product'=> $product], 201);
    }
}
