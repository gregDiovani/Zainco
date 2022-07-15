<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function all(Request $request)
    {
        $users_id = Auth::user()->id;
        $products = Product::where('users_id', $users_id)->with(['category', 'galleries'])->get();

        if ($products) {
            return ResponseFormatter::success(
                $products,
                'Data Produk berhasil diiambil'
            );
        } else {
            return ResponseFormatter::error(
                null,
                'Data produk tidak ada ',
                404
            );
        }

        // try {

        // } catch (Exception $error) {
        //     return ResponseFormatter::error([
        //         'message' => 'Something went wrong',
        //         'error' => $error

        //     ]);
        // }
    }

    public function makeProduct(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'price' => 'required|numeric',
            'description' => ['nullable', 'string', 'max:255'],
            'tags' => ['nullable', 'string', 'max:255'],
        ]);

        if (!ProductCategory::find($request->categories_id)) {
            return response()->json(['message' => 'category id tidak ada'], 400);
        }

        Product::create([
            'name' => $request->name,
            'price' => $request->price,
            'description' => $request->name,
            'tags' => $request->tags,
            'users_id' => Auth::user()->id,
            'categories_id' => $request->categories_id
        ]);

        $product = Product::where('name', $request->name)->first();

        return ResponseFormatter::success([
            'message' => 'Product berhasil dibuat',
            'product' => $product

        ]);
    }

    public function updateProduct(Request $request, $id)
    {
        try {
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'price' => ['required', 'double'],
                'description' => ['nullable', 'string', 'max:255'],
                'tags' => ['nullable', 'string', 'max:255'],
                'categories_id' => ['requires', 'double', 'max:255'],
            ]);

            $product = Product::findOrFail($id);

            $product->update([
                'name' => $request->name,
                'price' => $request->price,
                'description' => $request->description,
                'tags' => $request->tags,
                'categories_id' => $request->categories_id,
            ]);

            $data = ProductCategory::where('id', '=', $product->id)->get();


            return ResponseFormatter::success([
                'message' => 'Ketegori berhasil diperbaharui',
                'category' => $data

            ]);
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $error

            ]);
        }
    }
}
