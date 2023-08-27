<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            "id"=> $this->id,
            "price"=> $this->price,
            "quantity"=> $this->quantity,
            "discount"=> $this->discount,
            "name"=> $this->name,
            "brand"=> $this->brand,
            "type"=> $this->type,
            "status"=> $this->status,
            "featured"=> $this->featured,
            "created_at"=> $this->created_at,
            "updated_at"=> $this->updated_at,
            'image_url' => optional(optional($this->getMedia('image'))->first())->getFullUrl(),
        ];
    }
}
