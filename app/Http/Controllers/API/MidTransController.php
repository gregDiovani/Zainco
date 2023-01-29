<?php
namespace App\Http\Controllers\API;

use App\Models\User;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\ProductCategory;
use App\Models\TransactionItem;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Helpers\MidTransHelperPayload;
use Illuminate\Notifications\Notification;
use Exception;



class MidTransController extends Controller{


public static function createGopay(array $product){
    

    \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');

// $items = [];

// foreach ($product["produk_item"] as $item) {

//     $itemDetail = Product::where('id',$item['id'])->first();  
//     $new_item = array(
//         'price' => $itemDetail->price ,
//         'id' => $item['id'],
//         'quantity' => $item['quantity'],
//         'name'=> $itemDetail->name,
//     );
//     array_push($items, $new_item);
// }
    try{
    $gopay = MidTransHelperPayload::gopayPayload($product);


    $response = \Midtrans\CoreApi::charge($gopay);
    $response_code = intval($response->status_code);

    if($response_code == 201 || $response_code == 200){
    
    //masukan ke database
    $transaction = Transaction::create([
        'users_id' => $product['user'],
        'order_id' => $product['order_id'],
        'total_price' => $product['totalRp'],
        'status' => 'PENDING',
        'payment' => $product['jenis_payment'],
      
    ]);

    $produk_item = $product["produk_item"];
    foreach ($produk_item as $item) {
        TransactionItem::create([
            'users_id' => $product['user'],
            'products_id' => $item['id'],
            'transactions_id' => $transaction->id,
            'quantity' => $item['quantity']
        ]);
    }

        return ResponseFormatter::success($response);   
        }
    }catch(Exception $e){
        return ResponseFormatter::error([
            'message' => 'Something went wrong...pls try again'
        ]);
    }
}

    public static function createBank(array $product)
    {


        \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');

        // $items = [];

        // foreach ($product["produk_item"] as $item) {

        //     $itemDetail = Product::where('id',$item['id'])->first();  
        //     $new_item = array(
        //         'price' =>$itemDetail->price ,
        //         'id' => $item['id'],
        //         'quantity' => $item['quantity'],
        //         'name'=>$itemDetail->name,
        //     );
        //     array_push($items, $new_item);
        // }
        try {
            $bank = MidTransHelperPayload::BankPayload($product);
            $response = \Midtrans\CoreApi::charge($bank);

            $response_code = intval($response->status_code);
            if ($response_code == 201 || $response_code == 200) {

                //masukan ke database
                $transaction = Transaction::create([
                    'users_id' => $product['user'],
                    'order_id' => $product['order_id'],
                    'total_price' => $product['totalRp'],
                    'status' => 'PENDING',
                    'payment' => $product['jenis_payment'],

                ]);

                $produk_item = $product["produk_item"];
                foreach ($produk_item as $item) {
                    TransactionItem::create([
                        'users_id' => $product['user'],
                        'products_id' => $item['id'],
                        'transactions_id' => $transaction->id,
                        'quantity' => $item['quantity']
                    ]);
                }

                return ResponseFormatter::success($response);
            }
        } catch (Exception $e) {
            return ResponseFormatter::error([
                'message' => 'Something went wrong'
            ]);
        }

    }

public static function getStatus($orderid){

    \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');

        try {
            $status = \Midtrans\Transaction::status($orderid);

            $transaction = Transaction::where('order_id', $orderid)->first();

            //Update status jika success
            if ($status->transaction_status == 'success') {
                $transaction->update([
                    'status' => 'PAID'
                ]);
            }

            return $status;

        }catch(Exception $e){
            return ResponseFormatter::error([
                'message' => 'Something went wrong...pls try again'
            ]);

        }

        
    
  
}


    
}

?>