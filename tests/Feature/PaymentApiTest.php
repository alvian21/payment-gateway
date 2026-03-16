<?php

use App\Models\PaymentOrder;
use App\Models\PaymentTransaction;
use App\Models\PaymentTransactionBackup;
use Carbon\Carbon;
use Illuminate\Support\Facades\Event;

beforeEach(function () {
    $this->secToken = date('Ymd');
    $this->headers = [
        'Sec-Token' => $this->secToken,
    ];
});

test('order endpoint requires sec-token', function () {
    $expired = urlencode(now()->addDays(2)->format('Y-m-d\TH:i:sP'));
    $response = $this->get('/api/order?name=John&hp=08123&amount=100000&reff=REFF1&expired=' . $expired);
    $response->assertStatus(401)
        ->assertJson(['error' => 'Unauthorized']);
});

test('order endpoint returns correct response payload on success', function () {
    $expired = urlencode(now()->addDays(2)->format('Y-m-d\TH:i:sP'));
    $response = $this->get('/api/order?name=Budi&hp=0812345678&amount=100000&reff=REFFORDER123&expired=' . $expired, $this->headers);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'amount',
            'reff',
            'name',
            'expired',
            'code'
        ]);

    $data = $response->json();
    $this->assertEquals('102500', $data['amount']);
    $this->assertEquals('Budi', $data['name']);

    // Check database
    $order = PaymentOrder::where('reff', $data['reff'])->first();
    $this->assertNotNull($order);
    $this->assertEquals('100000', $order->base_amount);
    $this->assertEquals('102500', $order->amount);
});

test('payment endpoint successfully pays order and creates transactions', function () {
    Event::fake([\App\Events\PaymentTransactionCreated::class]);

    // Scaffold an order directly
    $order = PaymentOrder::create([
        'reff' => 'REFFPAYMENT123',
        'customer_name' => 'Alice',
        'hp' => '08987654321',
        'code' => '883408987654321',
        'base_amount' => 50000,
        'fee' => 2500,
        'amount' => 52500,
        'expired_at' => Carbon::now()->addDays(2),
        'status' => 'pending',
    ]);

    $this->withoutExceptionHandling();
    $response = $this->get("/api/payment?reff={$order->reff}&paid=52500", $this->headers);

    $response->assertStatus(200)
        ->assertJson([
            'amount' => '52500',
            'reff' => 'REFFPAYMENT123',
            'status' => 'paid',
        ]);

    Event::assertDispatched(\App\Events\PaymentTransactionCreated::class);

    $order->refresh();
    $this->assertEquals('paid', $order->status);
    $this->assertNotNull($order->paid_at);

    $transaction = PaymentTransaction::where('payment_order_id', $order->id)->first();
    $this->assertNotNull($transaction);
});

test('status endpoint returns proper json data', function () {
    $order = PaymentOrder::create([
        'reff' => 'REFFSTATUS123',
        'customer_name' => 'Bob',
        'hp' => '08111222333',
        'code' => '883408111222333',
        'base_amount' => 75000,
        'fee' => 2500,
        'amount' => 77500,
        'expired_at' => Carbon::now()->addDays(2),
        'status' => 'pending',
    ]);

    $response = $this->get('/api/status?reff=REFFSTATUS123', $this->headers);

    $response->assertStatus(200)
        ->assertJson([
            'amount' => '77500',
            'reff' => 'REFFSTATUS123',
            'name' => 'Bob',
            'code' => '883408111222333',
            'status' => 'pending'
        ]);
});

test('payment endpoint returns 403 on unknown reff', function () {
    $response = $this->get("/api/payment?reff=UNKNOWN123&paid=50000", $this->headers);
    $response->assertStatus(403)
        ->assertJson(['error' => 'Unknown reff']);
});

test('status endpoint returns 403 on unknown reff', function () {
    $response = $this->get("/api/status?reff=UNKNOWN123", $this->headers);
    $response->assertStatus(403)
        ->assertJson(['error' => 'Unknown reff']);
});
