<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCartItemApiRequest;
use App\Http\Requests\UpdateCartItemApiRequest;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $cart = Cart::firstOrCreate([
            'user_id' => $request->user()->id,
        ]);

        $cart->load('items.product');

        return response()->json([
            'data' => $cart,
        ]);
    }

    public function store(StoreCartItemApiRequest $request)
    {
        $cart = Cart::firstOrCreate([
            'user_id' => $request->user()->id,
        ]);

        $data = $request->validated();

        $item = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $data['product_id'])
            ->first();

        if ($item) {
            $item->increment('quantity', $data['quantity']);
        } else {
            $item = CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $data['product_id'],
                'quantity' => $data['quantity'],
            ]);
        }

        $item->load('product');

        return response()->json([
            'message' => 'Product added to cart successfully',
            'data' => $item,
        ], 201);
    }

    public function update(UpdateCartItemApiRequest $request, CartItem $cartItem) 
    {
        if ($cartItem->cart->user_id !== $request->user()->id) {
        return response()->json([
            'message' => 'Unauthorized',
        ], 403);
        }

        $cartItem->update([
            'quantity' => $request->validated()['quantity'],
        ]);

        $cartItem->load('product');

        return response()->json([
            'message' => 'Cart item updated successfully',
            'data' => $cartItem,
        ]);
    }

    public function destroy(Request $request, CartItem $cartItem)
    {
        if ($cartItem->cart->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        $cartItem->delete();

        return response()->json([
            'message' => 'Cart item removed successfully',
        ]);
    }
}
