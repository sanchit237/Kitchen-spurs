<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'createdAt' => date("Y-m-d H:i:s", strtotime($this->created_at)),
            'updatedAt' => date("Y-m-d H:i:s", strtotime($this->updated_at)),
        ];
    }
}
