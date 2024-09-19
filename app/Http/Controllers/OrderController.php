<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Models\Order;
use App\Models\Product;
use App\Models\Transaction;
use App\OrderStatus;
use App\TransactionType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(): \Inertia\Response|\Inertia\ResponseFactory
    {
        $orders = Order::with('product')->get();
        $totalSum = Order::sum('price');
        $totalSumCanceled = Order::sum('price');

        return inertia('Orders/Index', compact('orders', 'totalSum', 'totalSumCanceled'));
    }

    public function store(int $accountId, OrderRequest $request): RedirectResponse
    {
        $product = Product::findOrFail($request->product_id);

        DB::transaction(function () use ($request, $product, $accountId) {
            $order = Order::create($request->validated() + [
                    'account_id' => $accountId,
                    'base_price' => $product->price
                ]);

            Transaction::create([
                'account_id' => $order->account_id,
                'order_id' => $order->id,
                'amount' => $order->price,
                'type' => TransactionType::DEBIT->value
            ]);
        });

        return redirect()->back();
    }

    public function cancel(int $account, int $id): RedirectResponse
    {
        $order = Order::where(['account_id' => $account, 'id' => $id])
            ->whereNull('status')
            ->firstOrFail();

        DB::transaction(function () use ($order, $account) {
            $order->update(['status' => OrderStatus::CANCEL->value]);

            Transaction::whereOrderId($order->id)->firstOrFail()->delete();
        });

        return redirect()->back();
    }
}
