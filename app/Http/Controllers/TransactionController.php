<?php

namespace App\Http\Controllers;

use App\Enums\TransactionType;
use App\Http\Requests\TransactionRequest;
use App\Models\Transaction;
use App\Services\TransactionService;
use Illuminate\Http\RedirectResponse;

class TransactionController extends Controller
{
    public function __construct(
        private TransactionService $transactionService
    )
    {
    }

    public function index(): \Inertia\Response|\Inertia\ResponseFactory
    {
        $totalDebits = Transaction::debit()->sum('amount');
        $totalCredits = Transaction::credit()->sum('amount');

        $transactions = Transaction::with('account')
            ->orderByDesc('created_at')
            ->get()
            ->groupBy(fn($item) => $item->created_at->format('Y-m-d'));

        return inertia('Transactions/Index', compact(
            'transactions',
            'totalCredits',
            'totalDebits',
        ));
    }

    public function statistics(): \Inertia\Response|\Inertia\ResponseFactory
    {
        [
            $dayDebitSum,
            $weekDebitSum,
            $monthDebitSum
        ] = $this->transactionService->getStatisticsByType(TransactionType::DEBIT);

        [
            $dayCreditSum,
            $weekCreditSum,
            $monthCreditSum
        ] = $this->transactionService->getStatisticsByType(TransactionType::CREDIT);

        return inertia('Transactions/Statistics', compact(
            'dayDebitSum',
            'weekDebitSum',
            'monthDebitSum',
            'dayCreditSum',
            'weekCreditSum',
            'monthCreditSum'
        ));
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
