<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function store(Request $request)
    {
        $user = $request->user();

        $cart = Cart::where('user_id', $user->id)
            ->with('items.product')
            ->first();

        if (! $cart || $cart->items->isEmpty()) {
            return response()->json([
                'message' => 'Cart is empty.',
            ], 400);
        }

        $order = DB::transaction(function () use ($cart, $user) {

            $totalPrice = 0;

            foreach ($cart->items as $item) {

                $product = $item->product;

                if ($product->stock < $item->quantity) {
                    return response()->json([
                        'message' => "Stock for {$product->name} is insufficient.",
                    ], 400);
                }

                $subtotal = $product->price * $item->quantity;

                $totalPrice += $subtotal;
            }

            $order = Order::create([
                'user_id' => $user->id,
                'total_price' => $totalPrice,
                'status' => 'pending',
            ]);

            foreach ($cart->items as $item) {

                $product = $item->product;

                $price = $product->price;
                $subtotal = $price * $item->quantity;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $price,
                    'subtotal' => $subtotal,
                ]);

                $product->decrement('stock', $item->quantity);
            }

            $cart->items()->delete();

            return $order;
        });

        if ($order instanceof JsonResponse) {
            return $order;
        }

        return response()->json([
            'message' => 'Checkout successful.',
            'data' => $order->load('items.product'),
        ], 201);
    }
}
