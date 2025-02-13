<?php

namespace App\Http\Controllers;

use App\Events\OrderPlaced;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class OrderController extends Controller
{
    public function placeOrder() {
        $user = Auth::user();
        
        // Eager load product details to reduce queries
        $cartItems = Cart::with('product:id,price')->where('user_id', $user->id)->get();
    
        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'Cart is empty']);
        }
    
        // Calculate total price
        $totalPrice = $cartItems->sum(fn($item) => $item->product->price * $item->quantity);
    
        // Create order
        $order = Order::create([
            'user_id' => $user->id,
            'status' => 'pending',
            'total_price' => $totalPrice,
        ]);
    
        // Prepare bulk insert for better performance
        $orderItems = $cartItems->map(fn($cartItem) => [
            'order_id' => $order->id,
            'product_id' => $cartItem->product_id,
            'quantity' => $cartItem->quantity,
            'price' => $cartItem->product->price,
            'created_at' => now(),
            'updated_at' => now(),
        ])->toArray();
    
        OrderItem::insert($orderItems); // Bulk insert
    
        // Remove cart items
        Cart::where('user_id', $user->id)->delete();

        event(new OrderPlaced($order));
    
        return response()->json(['message' => 'Order placed successfully', 'order' => $order], 201);
    }
    

    public function vendorOrders(){
        $vendor = Auth::user();
        $order = Order::whereHas('items', function($query) use ($vendor){
            $query->whereHas('product', function($query) use ($vendor){
                $query->where('vendor_id', $vendor->id);
            });
        })
        ->with('items.product')
        ->get();

        return response()->json($order);
    }

        public function allOrders()
    {
        // Eager load order items and associated products, and user
        $orders = Order::with('items.product', 'user')->get();

        if ($orders->isEmpty()) {
            return response()->json(['message' => 'No orders found.'], 404);
        }

        return response()->json($orders);
    }
    use AuthorizesRequests;
    public function updateOrderStatus(Request $request, $orderId)
{
    $order = Order::findOrFail($orderId);

    // Ensure correct authorization method
    $this->authorize('updateOrderStatus', $order);

    $order->update([
        'status' => $request->status,
    ]);

    return response()->json(['message' => 'Order status updated successfully.']);
}


}
