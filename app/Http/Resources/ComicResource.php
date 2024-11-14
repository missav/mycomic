<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ComicResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name(),
            'recent_chapter_id' => $this->recent_chapter_id,
            'recent_chapter_title' => $this->recentChapterTitle(),
            'cover_image_path' => $this->coverImagePath(),
            'is_ended' => $this->is_ended,
        ];
    }
}
