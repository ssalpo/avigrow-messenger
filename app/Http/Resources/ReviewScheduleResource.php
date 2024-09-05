<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewScheduleResource extends JsonResource
{

    public static $wrap = null;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'chat_id' => $this->chat_id,
            'account_id' => $this->account_id,
            'send_at' => $this->send_at->format('d.m.Y H:i'),
            'created_at' => $this->created_at->format('d.m.Y H:i'),
        ];
    }
}
