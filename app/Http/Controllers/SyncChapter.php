<?php

namespace App\Http\Controllers;

use App\Concerns\WithUserUuid;
use App\Models\Chapter;
use App\Models\Record;

class SyncChapter
{
    use WithUserUuid;

    public function __invoke(Chapter $chapter): array
    {
        Record::updateOrCreate([
            'user_id' => $this->getUserUuid(),
            'comic_id' => $chapter->comic->id,
        ], [
            'chapter_id' => $chapter->id,
            'updated_at' => now(),
        ]);

        return $this->responseUserUuid();
    }
}
