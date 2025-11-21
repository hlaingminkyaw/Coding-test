<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        User::factory()->count(10)->create();
        Product::factory()->count(30)->create();

        // create some orders
        $users = User::all();
        $products = Product::all();

        foreach (range(1, 80) as $i) {
            $user = $users->random();
            $order = Order::create([
                'user_id' => $user->id,
                'status' => 'completed',
                'total_amount' => 0,
                'created_at' => now()->subDays(rand(0, 180))
            ]);
            $total = 0;
            foreach (range(1, rand(1, 4)) as $j) {
                $product = $products->random();
                $qty = rand(1, 5);
                $line = $product->price * $qty;
                $total += $line;
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'unit_price' => $product->price,
                    'line_total' => $line,
                ]);
            }
            $order->update(['total_amount' => $total, 'completed_at' => now()->subDays(rand(0, 180))]);

            // random transactions
            Transaction::create([
                'order_id' => $order->id,
                'amount' => $total,
                'method' => 'card',
                'ref' => 'TX' . rand(1000, 9999)
            ]);
        }
    }
}
