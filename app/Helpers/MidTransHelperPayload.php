<?php

namespace App\Helpers;

use App\Models\User;

class MidTransHelperPayload
{
    protected static $payload = [
        'transaction_details' => [
          'order_id' => null,
          'gross_amount' => null
        ],
        'payment_type'  => null,
        'customer_details'  =>null
      ];

      public static function gopayPayload(array $product)
      {

        $costumer = User::where('id',$product['user'])->first();

        $customer_detail = [
            'first_name'            =>  $costumer->name,
            'email'                     => $costumer->email,
            'phone'                     => $costumer->phone_number,
        ];

        self::$payload['transaction_details']['order_id'] = $product['order_id'];
        self::$payload['transaction_details']['gross_amount'] = $product['totalRp'];
        self::$payload['payment_type'] = $product['jenis_payment'];
        self::$payload['customer_details'] = $customer_detail;

        return self::$payload;
      }


      public static function BankPayload(array $product)
      {

    $bank = array(
      'bank_transfer' => array(
        'bank' => $product['bank'],
      )
    );
    

        $costumer = User::where('id',$product['user'])->first();

        $customer_detail = [
            'first_name'            =>  $costumer->name,
            'email'                     => $costumer->email,
            'phone'                     => $costumer->phone_number,
        ];

        self::$payload['transaction_details']['order_id'] = $product['order_id'];
        self::$payload['transaction_details']['gross_amount'] = $product['totalRp'];
        self::$payload['payment_type'] = 'bank_transfer';
        self::$payload['customer_details'] = $customer_detail;
        $payloadMerged = array_merge(self::$payload, $bank);

        return $payloadMerged;
      }


}




?>