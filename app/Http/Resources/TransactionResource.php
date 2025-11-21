<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
     public function toArray($request){
        return [
            'id'=>$this->id,
            'order_id'=>$this->order_id,
            'amount'=> (float)$this->amount,
            'method'=>$this->method,
            'ref'=>$this->ref,
            'created_at'=>$this->created_at,
        ];
    }
}
