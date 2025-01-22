<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Enums\ArticleStatusEnum;

class ArticleResource extends JsonResource
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
            'title' => $this->title,
            'content' => $this->content,
            'status' => ArticleStatusEnum::getStatusLabel($this->status),
            'created_at' => date("d M, Y", strtotime($this->created_at)),
            'updated_at' => date("d M, Y", strtotime($this->updated_at)),
        ];
    }
}
