<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionRequest;
use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(): \Inertia\Response|\Inertia\ResponseFactory
    {
        $transactions = Transaction::with('account')
            ->orderByDesc('created_at')
            ->get()
            ->groupBy(fn($item) => $item->created_at->format('Y-m-d'));

        return inertia('Transactions/Index', compact('transactions'));
    }

    public function store(TransactionRequest $request): RedirectResponse
    {
        Transaction::create($request->validated());

        return redirect()->back();
    }

    public function show(Transaction $transaction): \Inertia\Response|\Inertia\ResponseFactory
    {
        $transaction->load('account');

        return inertia('Transactions/Show', compact('transaction'));
    }

    public function edit(Transaction $transaction): \Inertia\Response|\Inertia\ResponseFactory
    {
        return inertia('Transactions/Edit', compact('transaction'));
    }

    public function update(Transaction $transaction, TransactionRequest $request): RedirectResponse
    {
        $transaction->update($request->validated());

        return redirect()->back();
    }

    public function destroy(Transaction $transaction): RedirectResponse
    {
        if ($transaction->order_id) {
            return redirect()->back();
        }

        $transaction->delete();

        return to_route('transactions.index');
    }
}
