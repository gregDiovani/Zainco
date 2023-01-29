<?php

namespace App\Http\Controllers\API;

use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\TransactionItem;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function all(Request $request)
    {
        $id = $request->input('id');
        $limit = $request->input('limit');
        $status = $request->input('status');

        if ($id) {

            $transaction = Transaction::with(['items.product'])->find($id);

            /// Jika Id product ada
            if ($transaction) {
                return ResponseFormatter::success(
                    $transaction,
                    'Data Transaksi berhasil diiambil'
                );
            } else {
                return ResponseFormatter::error(
                    null,
                    'Data Transaksi tidak ada',
                    404
                );
            }
        }

        $transaction = Transaction::with(['items.product'])->where('users_id', Auth::user()->id);

        if ($status) {
            $transaction->where('status', $status);
        }

        return ResponseFormatter::success(
            $transaction->paginate($limit),
            'Data List Transaksi berhasil diiambil'
        );
    }

    public function checkout(Request $request)
    {

        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'exists:products,id',
            'total_price' => 'required',
            'status' => 'required|in:PENDING,SUCCESS',
            'payment' => 'required|in:gopay,mandiri,bca,mandiri' /// jenis payment
        ]);

        $external_id = 'zainco-' . time();

        foreach ($request->items as $product) {

            $checkout = array(
                'order_id' =>  $external_id,
                'totalRp' => $request->total_price,
                'produk_item'   => $request->items,
                'user' => Auth::user()->id,
                'jenis_payment' =>$request->payment,
                
                );

                if($request->payment == 'mandiri' || $request->payment == 'bca'){
                $bank = array(
                    "bank" => $request->payment,
                    "va_number" =>$request->va
                );
                    $checkout+=$bank;  
                }
    
            if($request->payment == 'gopay'){
             return MidTransController::createGopay($checkout);
            }

            elseif($request->payment == 'mandiri' || $request->payment == 'bca'){
                return MidTransController::createBank($checkout);
            }

        }

    }

}
