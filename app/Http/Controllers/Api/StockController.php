<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StockRequest;
use App\Models\Product;

class StockController extends Controller
{
    public function update(StockRequest $request, Product $product)
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
