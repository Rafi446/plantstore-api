<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function update(Request $request, Product $product)
    {
        $product->update([
            'stock' => $request->stock,
        ]);
        
        return response()->json([
            'message' => 'Stock update successfully.',
            'data' => $product,
        ]);
    }
}
