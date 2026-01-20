<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WalletTransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'direction' => $this->direction,
            'amount' => $this->amount,
            'reference' => $this->reference,
            'description' => $this->description,
            'created_at' => $this->created_at,
        ];
    }
}
