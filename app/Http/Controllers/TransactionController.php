<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Order;
use App\Http\Requests\TransactionRequest;
use App\Http\Resources\TransactionResource;

class TransactionController extends Controller
{
    public function store(TransactionRequest $req)
    {
        $data = $req->validated();
        $transaction = Transaction::create($data);

        // Optionally mark order as paid
        $order = Order::find($data['order_id']);
        if ($order && $order->total_amount <= $order->transactions()->sum('amount')) {
            $order->status = 'paid';
            $order->save();
        }

        return (new TransactionResource($transaction))->response()->setStatusCode(201);
    }

    public function index()
    {
        return TransactionResource::collection(Transaction::with('order')->paginate(20));
    }

    public function show(Transaction $transaction)
    {
        return new TransactionResource($transaction->load('order'));
    }
}
