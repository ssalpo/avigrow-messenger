<?php

namespace App\Http\Controllers;

use App\CodeKeyType;
use App\Http\Requests\CodeKeyRequest;
use App\Jobs\SendMessageToExistingIds;
use App\Models\CodeKey;
use Illuminate\Http\RedirectResponse;

class CodeKeyController extends Controller
{
    public function index(): \Inertia\Response|\Inertia\ResponseFactory
    {
        $tabs = CodeKeyType::labels();
        $keys = CodeKey::whereNull('receipt_at')->orderByDesc('created_at')->get()->groupBy('product_type');

        return inertia("CodeKeys/Index", compact('tabs', 'keys'));
    }

    public function histories(): \Inertia\Response|\Inertia\ResponseFactory
    {
        $tabs = CodeKeyType::labels();
        $keys = CodeKey::whereNotNull('receipt_at')->orderByDesc('created_at')->get()->groupBy('product_type');

        return inertia("CodeKeys/History", compact('tabs', 'keys'));
    }

    public function store(CodeKeyRequest $request): RedirectResponse
    {
        CodeKey::create($request->validated());

        return redirect()->back();
    }

    public function markAsReceipt(CodeKey $codeKey): ?RedirectResponse
    {
        $codeKey->update([
            'receipt_by' => auth()->id(),
            'receipt_at' => now()
        ]);

        $message = <<<MSG
<b>Код был использован:</b>

{$codeKey->content}
MSG;

        SendMessageToExistingIds::dispatch($message);

        if(request('empty')) {
            return null;
        }

        return redirect()->back();
    }

    public function destroy(CodeKey $codeKey): RedirectResponse
    {
        $codeKey->delete();

        return redirect()->back();
    }

    public function restore(CodeKey $codeKey): RedirectResponse
    {
        $codeKey->update(['receipt_at' => null, 'receipt_by' => null]);

        $message = <<<MSG
<b>Код был восстановлен:</b>

{$codeKey->content}
MSG;


        SendMessageToExistingIds::dispatch($message);

        return redirect()->back();
    }
}
