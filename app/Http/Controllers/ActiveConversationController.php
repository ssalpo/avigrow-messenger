<?php

namespace App\Http\Controllers;

use App\Models\ActiveConversation;
use Illuminate\Http\JsonResponse;

class ActiveConversationController extends Controller
{
    public function getLists(): JsonResponse
    {
        $conversations = ActiveConversation::query()
            ->whereHas('account', fn($q) => $q->relatedToMe())
            ->orderByDesc('created_at')
            ->get();

        return response()->json($conversations);
    }

    public function destroy(int $id): void
    {
        ActiveConversation::query()
            ->whereHas('account', fn($q) => $q->relatedToMe())
            ->findOrFail($id)
            ?->delete();
    }
}
