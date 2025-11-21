<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Http\Requests\OrderRequest;
use App\Http\Resources\OrderResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Carbon\Carbon;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('user');

        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('user_id')) $query->where('user_id', $request->user_id);
        if ($request->filled('from') && $request->filled('to')) {
            $query->whereBetween('created_at', [$request->from, $request->to]);
        }

        // sorting
        if ($request->filled('sort')) {
            $s = $request->sort;
            $direction = str_starts_with($s, '-') ? 'desc' : 'asc';
            $s = ltrim($s, '-');
            $query->orderBy($s, $direction);
        }

        $orders = $query->with('items.product')->paginate($request->get('per_page', 15));
        return OrderResource::collection($orders);
    }

    public function store(OrderRequest $req)
    {
        $data = $req->validated();
        DB::beginTransaction();
        try {
            $total = 0;
            $order = Order::create([
                'user_id' => $data['user_id'],
                'status' => 'pending',
                'total_amount' => 0
            ]);

            foreach ($data['items'] as $it) {
                $product = Product::findOrFail($it['product_id']);
                $quantity = (int)$it['quantity'];
                $unit_price = $product->price;
                $line_total = $quantity * $unit_price;
                $total += $line_total;

                $order->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $unit_price,
                    'line_total' => $line_total,
                ]);

                // optionally decrement stock
                $product->decrement('stock', $quantity);
            }

            $order->update(['total_amount' => $total]);

            DB::commit();
            return (new OrderResource($order->load('items.product')))->response()->setStatusCode(201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to create order', 'error' => $e->getMessage()], 500);
        }
    }

    public function updateStatus(Request $req, Order $order)
    {
        $req->validate(['status' => 'required|in:pending,paid,completed,cancelled']);
        $old = $order->status;
        $order->status = $req->status;
        if ($req->status === 'completed') {
            $order->completed_at = Carbon::now();
        }
        $order->save();


        return new OrderResource($order->load('items.product', 'transactions'));
    }

    public function show(Order $order)
    {
        return new OrderResource($order->load('items.product', 'transactions'));
    }

    public function destroy(Order $order)
    {
        $order->delete();
        return response()->json(null, 204);
    }
}
