<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
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
            'product_id' => $this->product_id,
            'user_id' => $this->user_id,
            'comment' => $this->comment,
            'attachment' => json_decode($this->attachment),
            'rating' => $this->rating,
            'order_id' => $this->order_id,
            'customer' => $this->customer,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
