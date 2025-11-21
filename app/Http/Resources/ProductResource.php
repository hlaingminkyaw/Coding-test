<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
     public function toArray($request){
        return [
            'id'=>$this->id,
            'name'=>$this->name,
            'price'=> (float)$this->price,
            'stock'=>$this->stock,
            'description'=>$this->description,
            'created_at'=>$this->created_at,
        ];
    }
}
