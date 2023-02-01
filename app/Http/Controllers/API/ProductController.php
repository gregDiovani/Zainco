<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\ProductCategory;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProductController extends Controller
{
    public function all(Request $request)
    {
        $users_id = Auth::user()->id;
        $products = Product::where('users_id', $users_id)->with(['category', 'galleries'])->paginate(5);

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

            $product = Product::findOrFail($id);

            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'price' => ['required', 'numeric'],
                'description' => ['nullable', 'string', 'max:255'],
                'tags' => ['nullable', 'string', 'max:255'],
                'categories_id' => ['required', 'numeric', 'max:255','exists:product_categories,id'],
            ]);

            
            $product->update([
                'name' => $request->name,
                'price' => $request->price,
                'description' => $request->description,
                'tags' => $request->tags,
                'categories_id' => $request->categories_id,
            ]);
            

            
            $data = Product::where('id', '=', $product->id)->with('category')->get();

            return ResponseFormatter::success([
                'message' => 'Produk berhasil diperbaharui',
                'data' => $data

            ]);
        } catch (ModelNotFoundException  $error) {
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $error
            ]);
        }
    }

    public function destroy($id)
    {

        try{
            $product = Product::findOrFail($id);

             $product->delete();

                return ResponseFormatter::success([
                    'message' => 'Product berhasil dihapus'
                ]);

            }catch(Exception $e){
                return ResponseFormatter::error([
                    'message' => 'Something went wrong'
                ]);

            }

        

    }
}
