<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
     public function toArray($request){
        return [
            'id'=>$this->id,
            'product'=> new ProductResource($this->whenLoaded('product')),
            'quantity'=>$this->quantity,
            'unit_price'=> (float)$this->unit_price,
            'line_total'=> (float)$this->line_total,
        ];
    }
}
