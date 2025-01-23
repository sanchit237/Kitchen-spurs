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
            'slug' => $this->slug,
            'content' => $this->content,
            'summary' => $this->summary,
            'status' => ArticleStatusEnum::getStatusLabel($this->status),
            'publishedDate' => $this->published_date,
            'createdBy' => $this->user->name,
            'categories' => $this->categories->pluck('name')->implode(', '),
            'createdAt' => date("Y-m-d H:i:s", strtotime($this->created_at)),
            'updatedAt' => date("Y-m-d H:i:s", strtotime($this->updated_at)),
        ];
    }
}
