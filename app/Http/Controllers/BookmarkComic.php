<?php

namespace App\Http\Controllers;

use App\Concerns\WithUserUuid;
use App\Models\Comic;
use App\Models\Record;

class BookmarkComic
{
    use WithUserUuid;

    public function __invoke(Comic $comic): array
    {
        Record::updateOrCreate([
            'user_id' => $this->getUserUuid(),
            'comic_id' => $comic->id,
        ], [
            'has_bookmarked' => true,
        ]);

        return $this->responseUserUuid();
    }
}
