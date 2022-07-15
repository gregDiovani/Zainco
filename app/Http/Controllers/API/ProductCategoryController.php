<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductCategoryController extends Controller
{
    public function all(Request $request)
    {
        $users_id = Auth::user()->id;
        $categories = ProductCategory::where('users_id', $users_id)->with(['products'])->get();

        if ($categories) {
            return ResponseFormatter::success(
                $categories,
                'Data Kategori berhasil diiambil'
            );
        } else {
            return ResponseFormatter::error(
                null,
                'Data Kategori tidak ada ',
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

    // /**
    //  * Show the form for creating a new resource.
    //  *
    //  * @return \Illuminate\Http\Response
    //  */
    // public function create()
    // {
    //     //
    // }

    public function makeCategory(Request $request)
    {
        try {
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
            ]);

            ProductCategory::create([
                'name' => $request->name,
                'users_id' => Auth::user()->id,
            ]);

            $productcategory = ProductCategory::where('name', $request->name)->first();


            return ResponseFormatter::success([
                'message' => 'Ketegori berhasil dibuat',
                'category' => $productcategory

            ]);
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $error

            ]);
        }
    }

    public function updateProductCategory(Request $request, $id)
    {
        try {
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
            ]);

            $category = ProductCategory::findOrFail($id);

            $category->update([
                'name' => $request->name,
            ]);

            $data = ProductCategory::where('id', '=', $category->id)->get();


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

    public function destroy($id)
    {
        $category = ProductCategory::findOrFail($id);

        $data = $category->destroy();

        if ($data) {
            ResponseFormatter::success([
                'message' => 'Ketegori berhasil dihapus'
            ]);
        } else {
            return ResponseFormatter::error([
                'message' => 'Something went wrong'
            ]);
        }
    }
}
