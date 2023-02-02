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
use App\Models\TransactionHistory;
use Illuminate\Notifications\Notification;
use Exception;



class PaymentProcessController extends Controller{

    protected $product = array();


    public function __construct(?array $product)
    {
        $this->product = $product;

    }

// public  function createPaymentShopePay(){

    

    //     \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
    
    // // $items = [];
    
    // // foreach ($product["produk_item"] as $item) {
    
    // //     $itemDetail = Product::where('id',$item['id'])->first();  
    // //     $new_item = array(
    // //         'price' => $itemDetail->price ,
    // //         'id' => $item['id'],
    // //         'quantity' => $item['quantity'],
    // //         'name'=> $itemDetail->name,
    // //     );
    // //     array_push($items, $new_item);
    // // }
    //     try{
    //     $shopePay = MidTransHelperPayload::gopayPayload($this->product);
    
    //     $response = \Midtrans\CoreApi::charge($shopePay);
        
    //     $response_code = intval($response->status_code);
    
    //     if($response_code == 201 || $response_code == 200){
        
    //     //masukan ke database
    //     $transaction = Transaction::create([
    //         'users_id' => $this->product['user'],
    //         'order_id' => $this->product['order_id'],
    //         'total_price' =>$this->product['totalRp'],
    //         'status' => 'PENDING',
    //         'payment' => $this->product['jenis_payment'],
          
    //     ]);
    
    //     $produk_item = $this->product["produk_item"];
    //     foreach ($produk_item as $item) {
    //         TransactionItem::create([
    //             'users_id' => $this->product['user'],
    //             'products_id' =>$item['id'],
    //             'transactions_id' => $transaction->id,
    //             'quantity' => $item['quantity']
    //         ]);
    //     }
    //         return ResponseFormatter::success($response, 'Transaksi berhasil dibuat');
    
    //         }
    //     }catch(Exception $e){
    //         return ResponseFormatter::error([
    //             'message' => 'Something went wrong...pls try again'
    //         ]);
    //     }
    // }


// public  function createPaymentBank(){


//         \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');


//         try {
//             $bank = MidTransHelperPayload::BankPayload($this->product);
//             p
//             $paymentUrl = \Midtrans\Snap::createTransaction($bank)->redirect_url;
           
//                 //masukan ke database
//                 $transaction = Transaction::create([
//                     'users_id' => $this->product['user'],
//                     'order_id' =>$this->product['order_id'],
//                     'total_price' => $this->product['totalRp'],
//                     'status' => 'PENDING',
//                     'payment' => $this->product['jenis_payment'],

//                 ]);

//                 $produk_item = $this->product["produk_item"];
//                 foreach ($produk_item as $item) {
//                     TransactionItem::create([
//                         'users_id' => $this->product['user'],
//                         'products_id' => $item['id'],
//                         'transactions_id' => $transaction->id,
//                         'quantity' => $item['quantity']
//                     ]);
//                 }

//                 return ResponseFormatter::success($response);
//             }
//         } catch (Exception $e) {
//             return ResponseFormatter::error([
//                 'message' => 'Something went wrong'
//             ]);
//         }

// }


public  function createPaymentTunai(){

        try {

            $transaction = Transaction::create([
                'users_id' => $this->product['user'],
                'order_id' => $this->product['order_id'],
                'total_price' => $this->product['totalRp'],
                'status' => 'PENDING',
                'payment' => $this->product['jenis_payment'],

            ]);

            $produk_item = $this->product["produk_item"];
            foreach ($produk_item as $item) {
                TransactionItem::create([
                    'users_id' => $this->product['user'],
                    'products_id' => $item['id'],
                    'transactions_id' => $transaction->id,
                    'quantity' => $item['quantity']
                ]);

            }

            return ResponseFormatter::success($transaction->load('items.product'), 'Transaksi berhasil');


        }catch(Exception $e){
            return ResponseFormatter::error([
                'message' => 'Something went wrong'
            ]);
        }
}

// public  function createPaymentOnline(){

    

//     \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');

//     try{
//     $gopay = MidTransHelperPayload::MidtransPayload($this->product);
//     $paymentUrl = \Midtrans\Snap::createTransaction($gopay)->redirect_url;

//     //masukan ke database
//     $transaction = Transaction::create([
//         'users_id' => $this->product['user'],
//         'order_id' => $this->product['order_id'],
//         'total_price' =>$this->product['totalRp'],
//         'status' => 'PENDING',
//         'payment' => $this->product['jenis_payment'],
      
//     ]);

//     $produk_item = $this->product["produk_item"];
//     foreach ($produk_item as $item) {
//         TransactionItem::create([
//             'users_id' => $this->product['user'],
//             'products_id' =>$item['id'],
//             'transactions_id' => $transaction->id,
//             'quantity' => $item['quantity']
//         ]);
//     }

//     return ResponseFormatter::success([
//         'redirect-url'=> $paymentUrl]
//         , 'Transaksi berhasil dibuat');
//         }catch(Exception $e ){
//             return ResponseFormatter::error([
//                 'message' => 'Something went wrong'
//             ]);

//         }
  
// }








}

?>