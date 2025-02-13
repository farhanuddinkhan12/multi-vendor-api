<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index(){
        $user = Auth::user()->id;
        // return response()->json($user);
        // $cartItem = $user->carts()->with('product')->get();
        $cartItem = Cart::where('user_id', $user)->with('product')->get();
        return response()->json($cartItem);
    }
    public function store(Request $request){
        $product = Product::findOrFail($request->product_id);

        $existingCartItem = Cart::where('user_id', Auth::id())
                                ->where('product_id', $product->id)
                                ->first();

        if($existingCartItem){
            $existingCartItem->quantity += $request->quantity;
            $existingCartItem->save();
        }else{
            Cart::create([
                'user_id' => Auth::id(),
                'product_id' => $product->id,
                'quantity' => $request->quantity,
            ]);
        }

        return response()->json(['message' => 'Product added to cart successfully']);
    }
    public function destroy($id){
        $cart = Cart::findOrFail($id);
        $cart->delete();
        return response()->json(['message'=> 'Cart Deleted successfully']);
    }
}
