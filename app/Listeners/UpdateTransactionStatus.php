<?php

namespace App\Listeners;

use App\Events\PaymentSuccess;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateTransactionStatus
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\PaymentSuccess  $event
     * @return void
     */
    public function handle(PaymentSuccess $event)
    {

        \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');


        $notif = \Midtrans\Transaction::status($event->transaction_id);


        $transaction = $notif->transaction_status;
        $type = $notif->payment_type;
        $order_id = $notif->order_id;
        $fraud = $notif->fraud_status;

        if ($transaction == 'capture') {
            // For credit card transaction, we need to check whether transaction is challenge by FDS or not
            if ($type == 'credit_card') {
                if ($fraud == 'challenge') {

                } else {

                    Transaction::where('order_id', $event->transaction_id)
                        ->update(['status' => 'PAID',
                        'updated_at'=> Carbon::now()]);

                }
            }
        } else if ($transaction == 'settlement') {


            Transaction::where('order_id', $event->transaction_id)
                ->update(['status' => 'SETTLEMENT',
                'updated_at'=> Carbon::now()]);
        } else if ($transaction == 'pending') {

            Transaction::where('order_id', $event->transaction_id)
                ->update(
                    ['status' => 'PENDING',
                    'updated_at'=> Carbon::now()]);

        } else if ($transaction == 'deny') {

            Transaction::where('order_id', $event->transaction_id)
                ->update(['status' => 'DENIED']);

        } else if ($transaction == 'expire') {

            Transaction::where('order_id', $event->transaction_id)
                ->update(['status' => 'EXPIRED',
                          'updated_at'=> Carbon::now()]);

        } else if ($transaction == 'cancel') {


            Transaction::where('order_id', $event->transaction_id)
                ->update(['status' => 'CANCELED',
                          'updated_at'=> Carbon::now() ]);
        }

    }
}
