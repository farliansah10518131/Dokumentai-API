<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\ProductCategory;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;

class ProductCategoryController extends Controller
{
    public function all(Request $request)
    {
        $id = $request->input('id');
        $name = $request->input('name');
        $limit = $request->input('limit');
        $show_product = $request->input('show_product');

        if ($id) {
            $categories = ProductCategory::with(['products'])->find($id);
            if ($categories) {
                return ResponseFormatter::success(
                    $categories,
                    'Data product categories berhasil diambil',
                );
            } else {
                return ResponseFormatter::error(
                    $categories,
                    'Data product categories tidak berhasil diambil',
                );
            }
        }

        $categories = ProductCategory::query();
        if ($name) {
            $categories->where('name', 'LIKE', '%' . $name . '%');
        }

        if ($show_product) {
            $categories->with(['products']);
        }
        return ResponseFormatter::success(
            $categories->paginate($limit),
            'Data kategori berhasil diambil'
        );
    }
}
