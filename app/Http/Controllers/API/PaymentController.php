<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PaymentOrder;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use App\Models\PaymentTransaction;
use App\Events\PaymentTransactionCreated;

class PaymentController extends Controller
{
    public function orderPayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|gt:0',
            'reff' => 'required|string',
            'expired' => 'required|date_format:Y-m-d\TH:i:sP|after:now',
            'name' => 'required|string',
            'hp' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $reffValidator = Validator::make($request->all(), [
            'reff' => 'unique:payment_orders,reff',
        ]);

        if ($reffValidator->fails()) {
            return response()->json(['error' => 'Reff already exists'], 422);
        }

        $amount = (int) $request->get('amount');
        $fee = 2500;
        $totalAmount = $amount + $fee;
        $code = '8834' . $request->get('hp');

        $order = PaymentOrder::create([
            'reff' => $request->get('reff'),
            'customer_name' => $request->get('name'),
            'hp' => $request->get('hp'),
            'code' => $code,
            'base_amount' => $amount,
            'fee' => $fee,
            'amount' => $totalAmount,
            'expired_at' => Carbon::parse($request->get('expired')),
            'status' => 'pending',
        ]);

        return response()->json([
            'amount' => (string) $totalAmount,
            'reff' => $request->get('reff'),
            'expired' => Carbon::parse($request->get('expired'))->format('Y-m-d\TH:i:sP'),
            'name' => $request->get('name'),
            'code' => $code,
        ]);
    }

    public function payment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reff' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Reff is required'], 422);
        }

        $reff = $request->get('reff');
        $order = PaymentOrder::where('reff', $reff)->first();

        if (!$order) {
            return response()->json(['error' => 'Unknown reff'], 403);
        }

        if ($order->status === 'paid') {
            return response()->json(['error' => 'Double payment rejected'], 403);
        }

        $now = Carbon::now();
        $isExpired = $now->greaterThan($order->expired_at);
        $status = $isExpired ? 'expired' : 'paid';

        $order->status = $status;
        if ($status === 'paid') {
            $order->paid_at = clone $now;
        }
        $order->save();

        $transaction = PaymentTransaction::create([
            'payment_order_id' => $order->id,
            'reff' => $order->reff,
            'status' => $status,
            'source' => 'api',
            'amount' => $order->amount,
            'customer_name' => $order->customer_name,
            'code' => $order->code,
            'expired_at' => $order->expired_at,
        ]);

        event(new PaymentTransactionCreated($transaction));

        return response()->json([
            'amount' => (string) $order->amount,
            'reff' => $order->reff,
            'name' => $order->customer_name,
            'code' => $order->code,
            'status' => $status,
        ]);
    }

    public function checkStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reff' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Reff is required'], 422);
        }

        $order = PaymentOrder::where('reff', $request->get('reff'))->first();

        if (!$order) {
            return response()->json(['error' => 'Unknown reff'], 403);
        }

        $response = [
            'amount' => (string) $order->amount,
            'reff' => $order->reff,
            'name' => $order->customer_name,
            'expired' => Carbon::parse($order->expired_at)->format('Y-m-d\TH:i:sP'),
        ];

        if ($order->paid_at) {
            $response['paid'] = Carbon::parse($order->paid_at)->format('Y-m-d\TH:i:sP');
        }

        $response['code'] = $order->code;
        $response['status'] = $order->status;

        return response()->json($response);
    }
}
