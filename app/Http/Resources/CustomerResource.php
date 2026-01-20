<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
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
            'f_name' => $this->f_name,
            'l_name' => $this->l_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'image' => $this->image,
            'is_phone_verified' => (int)$this->is_phone_verified ?? null,
            'email_verified_at' => $this->email_verified_at,
            'updated_at' => $this->updated_at,
            'email_verification_token' => $this->email_verification_token,
            'cm_firebase_token' => $this->cm_firebase_token,
            'login_medium' => $this->login_medium,
            'wallet_balance' => (double)$this->userAccount?->wallet_balance ?? 0,
        ];
    }
}
