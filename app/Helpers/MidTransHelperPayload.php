<?php

namespace App\Helpers;

use App\Models\User;
use App\Models\Product;

class MidTransHelperPayload
{
    protected static $payload = [
        'transaction_details' => [
          'order_id' => null,
          'gross_amount' => null
        ],
        // 'payment_type'  => null,
        'customer_details'  =>null,
        'items' => null
      ];

      public static function MidtransPayload(array $product)
      {
        

        $items = [];

        foreach ($product["produk_item"] as $item) {

            $itemDetail = Product::where('id',$item['id'])->first();  
            $new_item = array(
                'price' => $itemDetail->price ,
                'id' => $item['id'],
                'quantity' => $item['quantity'],
                'name'=> $itemDetail->name,
            );
            array_push($items, $new_item);
        }


        $costumer = User::where('id',$product['user'])->first();

        $customer_detail = [
            'first_name'            =>  $costumer->name,
            'email'                     => $costumer->email,
            'phone'                     => $costumer->phone_number,
        ];

        self::$payload['transaction_details']['order_id'] = $product['order_id'];
        self::$payload['transaction_details']['gross_amount'] = $product['totalRp'];
        // self::$payload['payment_type'] = $product['jenis_payment'];
        self::$payload['customer_details'] = $customer_detail;
        self::$payload['items'] = $items;

        return self::$payload;
      }


    //   public static function BankPayload(array $product)
    //   {

    // $bank = array(
    //   'bank_transfer' => array(
    //     'bank' => $product['bank'],
    //   )
    // );
    

    //     $costumer = User::where('id',$product['user'])->first();

    //     $customer_detail = [
    //         'first_name'            =>  $costumer->name,
    //         'email'                     => $costumer->email,
    //         'phone'                     => $costumer->phone_number,
    //     ];

    //     self::$payload['transaction_details']['order_id'] = $product['order_id'];
    //     self::$payload['transaction_details']['gross_amount'] = $product['totalRp'];
    //     self::$payload['payment_type'] = 'bank_transfer';
    //     self::$payload['customer_details'] = $customer_detail;
    //     $payloadMerged = array_merge(self::$payload, $bank);

    //     return $payloadMerged;
    //   }


}




?>