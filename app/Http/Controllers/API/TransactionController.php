<?php

namespace App\Http\Controllers\API;

use Carbon\Carbon;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Events\PaymentSuccess;
use App\Models\TransactionItem;
use Illuminate\Validation\Rule;
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
            'items.*.price' => 'required|numeric',
            'items.*.quantity' => 'required|numeric',
            'total_price' => 'required',
            'status' => 'required|in:PENDING,SUCCESS',
            'payment' => 'required' /// jenis payment
        ]);


        $total_price = 0;
        $total_quantity = 0;
        
        for ($i = 0; $i < count($request->items); $i++) {
            $item = $request->items[$i];

            /// Temukan harga sesuai di database
            $product = Product::where('id', $item['id'])->first();

            if (!$product || $product->price != $item['price']) {
                return ResponseFormatter::error([
                    'message' => 'Price tidak sesuai'
                ]);

            }
            
            $total_price += $item['price'] * $item['quantity'];
            $total_quantity += $item['quantity'];

        }

        if($request->total_price != $total_price ){
            return ResponseFormatter::error([
            'message' => 'Total Rupiah tidak sesuai']);
        }




        foreach ($request->items as $product) {

            $checkout = array(
                'order_id' =>  $request->order_id,
                'totalRp' => $request->total_price,
                'produk_item'   => $request->items,
                'user' => Auth::user()->id,
                'jenis_payment' =>$request->payment
            );



            if ($request->payment == 'tunai') {

                $payment = new PaymentProcessController($checkout);
                return $payment->createPaymentTunai();

            }


           
            // }elseif($request->payment == 'gopay' || $request->payment == 'mandiri' || $request->payment == 'bca' ){
            //     $payment = new PaymentProcessController($checkout);
            //     return $payment->createPaymentOnline();

            // }

            
            // } elseif ($request->payment == 'gopay') {
            //     $payment = new PaymentProcessController($checkout);
            //     return $payment->createPaymentGopay();

            // }

            // elseif($request->payment == 'mandiri' 
            // || $request->payment == 'bca')
            // {
            //     $bank = array(
            //         "bank" => $request->payment,
            //         "va_number" =>$request->va
            //     );
            //         $checkout+=$bank; 

            //         $payment = new PaymentProcessController($checkout);
            //         return $payment->createPaymentBank();

                   
            // }

        }

    }

   

    public function konfirmasiPembayaranTunai(Request $request,$id){
        
        $transaction = Transaction::find($id);

        if ($transaction) {
            $transaction->update([
                'status' => 'PAID',
                'kembalian' => $request->kembalian,
                'updated_at' => Carbon::now()
            ]);

            return ResponseFormatter::success(
                $transaction,
            );


        }else{
            return ResponseFormatter::error(
                null,
                'Data Transaksi Tunai tidak ada',
                404
            );

        }

    }



    /// MIDTRANS
    
     // public function checkstatus($transaction_id){

    //     $transaction = Transaction::where('order_id',$transaction_id)->first();
    //     if ($transaction) {
    //         event(new PaymentSuccess($transaction_id));

    //         return ResponseFormatter::success(
    //             $transaction,
    //             'Data Transaksi user berhasil diambil'
    //         );
    //     } else {
    //         return ResponseFormatter::error(
    //             null,
    //             'Data Transaksi tidak ada',
    //             404
    //         );
    //     }


    // }



}
