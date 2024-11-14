<?php

namespace App\Http\Controllers;

use App\Concerns\WithUserUuid;
use App\Models\Comic;
use App\Models\Record;

class SyncComic
{
    use WithUserUuid;

    public function __invoke(Comic $comic): array
    {
        $comic->views();

        $record = Record::query()
            ->where('user_id', $this->getUserUuid())
            ->where('comic_id', $comic->id)
            ->first();

        return $this->responseUserUuid([
            'isLoggedIn' => auth()->check(),
            'hasBookmarked' => $record && $record->has_bookmarked,
            'recentChapterId' => $record?->chapter_id,
        ]);
    }
}