<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = [
            [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'john.doe@example.com',
                'phone' => '+1234567890',
                'password' => Hash::make('password'),
                'accepts_marketing' => true,
                'total_spent' => 250.50,
                'order_count' => 3,
                'last_order_at' => now()->subDays(5),
            ],
            [
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'email' => 'jane.smith@example.com',
                'phone' => '+1234567891',
                'password' => Hash::make('password'),
                'accepts_marketing' => false,
                'total_spent' => 150.75,
                'order_count' => 2,
                'last_order_at' => now()->subDays(10),
            ],
            [
                'first_name' => 'Michael',
                'last_name' => 'Johnson',
                'email' => 'michael.johnson@example.com',
                'phone' => '+1234567892',
                'password' => Hash::make('password'),
                'accepts_marketing' => true,
                'total_spent' => 89.99,
                'order_count' => 1,
                'last_order_at' => now()->subDays(15),
            ],
            [
                'first_name' => 'Emily',
                'last_name' => 'Davis',
                'email' => 'emily.davis@example.com',
                'phone' => '+1234567893',
                'password' => Hash::make('password'),
                'accepts_marketing' => true,
                'total_spent' => 0,
                'order_count' => 0,
                'last_order_at' => null,
            ],
            [
                'first_name' => 'Demo',
                'last_name' => 'User',
                'email' => 'demo@example.com',
                'phone' => '+1234567894',
                'password' => Hash::make('password'),
                'accepts_marketing' => false,
                'total_spent' => 0,
                'order_count' => 0,
                'last_order_at' => null,
            ],
        ];

        foreach ($customers as $customer) {
            Customer::create($customer);
        }
    }
}