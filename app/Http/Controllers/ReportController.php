<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $req)
    {
        $cacheKey = 'report_' . md5(json_encode($req->all()));
        $data = Cache::remember($cacheKey, 3600, function () use ($req) {
            $start = $req->get('from', now()->subMonths(6)->toDateString());
            $end = $req->get('to', now()->toDateString());

            // total sales in range
            $totalSales = Order::whereBetween('created_at', [$start, $end])->sum('total_amount');

            // monthly chart (group by year-month)
            $monthly = Order::select(DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"), DB::raw('SUM(total_amount) as total'))
                ->whereBetween('created_at', [$start, $end])
                ->groupBy('month')
                ->orderBy('month')
                ->get();

            // daily breakdown
            $daily = Order::select(DB::raw("DATE(created_at) as day"), DB::raw('SUM(total_amount) as total'))
                ->whereBetween('created_at', [$start, $end])
                ->groupBy('day')
                ->orderBy('day')
                ->get();

            // top selling products
            $topProducts = OrderItem::select('product_id', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(line_total) as total_sales'))
                ->whereHas('order', function ($q) use ($start, $end) {
                    $q->whereBetween('created_at', [$start, $end]);
                })
                ->groupBy('product_id')
                ->orderByDesc('total_qty')
                ->limit(10)
                ->with('product')
                ->get();

            return [
                'total_sales' => (float)$totalSales,
                'monthly' => $monthly,
                'daily' => $daily,
                'top_products' => $topProducts->map(fn($p) => [
                    'product_id' => $p->product_id,
                    'name' => $p->product->name ?? null,
                    'total_qty' => $p->total_qty,
                    'total_sales' => $p->total_sales,
                ]),
            ];
        });

        return response()->json($data);
    }

    public function salesByRange(Request $req)
    {
        $req->validate(['from' => 'required|date', 'to' => 'required|date']);
        $sum = Order::whereBetween('created_at', [$req->from, $req->to])->sum('total_amount');
        return response()->json(['total_sales' => (float)$sum]);
    }
}
