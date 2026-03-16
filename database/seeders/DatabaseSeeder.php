<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Admin Demo',
            'email' => 'admin@demo.com',
            'password' => Hash::make('password'),
        ]);

        for ($i = 1; $i <= 15; $i++) {
            $baseAmount = rand(50000, 500000);
            $fee = 2500;
            \App\Models\PaymentOrder::create([
                'reff' => 'REFF' . rand(10000, 99999) . $i,
                'customer_name' => 'Customer ' . $i,
                'hp' => '081234567' . sprintf('%02d', $i),
                'code' => '8834081234567' . sprintf('%02d', $i),
                'base_amount' => $baseAmount,
                'fee' => $fee,
                'amount' => $baseAmount + $fee,
                'expired_at' => Carbon::now()->addDays(rand(1, 5)),
                'status' => ['pending', 'paid', 'expired'][rand(0, 2)],
                'flagged_at' => rand(0, 1) ? Carbon::now() : null,
            ]);
        }
    }
}
