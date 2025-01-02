<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostDetailResource extends JsonResource
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
            'news_content' => $this->news_content,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'author' => $this->author,
            'writer' => $this->whenLoaded('writer'),
            'total_comments' => $this->comments->count(),
            'comments' => $this->whenLoaded('comments', function () {
                return $this->comments->loadMissing('commentator:id,username');
            }),


        ];
    }
}
