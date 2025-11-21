<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
   public function toArray($request){
        return [
            'id'=>$this->id,
            'user_id'=>$this->user_id,
            'total_amount'=> (float)$this->total_amount,
            'status'=>$this->status,
            'completed_at'=>$this->completed_at,
            'items' => OrderItemResource::collection($this->whenLoaded('items')),
            'transactions' => TransactionResource::collection($this->whenLoaded('transactions')),
            'created_at'=>$this->created_at,
        ];
    }
}
