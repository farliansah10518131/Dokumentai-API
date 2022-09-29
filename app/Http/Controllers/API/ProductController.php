<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function all(Request $request)
    {
        $id = $request->input('id');
        $limit = $request->input('limit');
        $name = $request->input('name');
        $description = $request->input('description');
        $tags = $request->input('tags');
        $categories = $request->input('categories');
        $price_from = $request->input('price_from');
        $price_to = $request->input('price_to');

        if ($id) {
            $product = Product::with(['category', 'galleries'])->find($id);

            if ($product) {
                return ResponseFormatter::success(
                    $product,
                    'Data berhasil diambil',
                );
            } else {
                return ResponseFormatter::error(
                    null,
                    'Data tidak terambil',
                    400
                );
            }
        }

        $product = Product::with(['category', 'galleries']);
        if ($name) {
            $product->where('name', 'LIKE', '%' . $name . '%');
        }
        if ($description) {
            $product->where('description', 'LIKE', '%' . $product . '%');
        }
        if ($tags) {
            $product->where('tags', 'LIKE', '%' . $tags . '%');
        }
        if ($price_from) {
            $product->where('price_from', '>=' . $price_from);
        }
        if ($price_to) {
            $product->where('price_to', '<=' . $price_to);
        }
        if ($categories) {
            $product->where('categories', $categories);
        }
        return ResponseFormatter::success(
            $product->paginate($limit),
            'Data berhasil diambil'
        );
    }
}
