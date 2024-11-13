<?php

namespace App\Http\Controllers;

use App\Concerns\WithUserUuid;
use App\Models\Comic;
use App\Models\Record;
use App\Recombee\Recombee;
use Recombee\RecommApi\Requests\DeleteBookmark;

class UnbookmarkComic
{
    use WithUserUuid;

    public function __invoke(Comic $comic): array
    {
        $record = Record::query()
            ->where('user_id', $this->getUserUuid())
            ->where('comic_id', $comic->id)
            ->first();

        if ($record->chapter_id) {
            $record->update(['has_bookmarked' => false]);
        } else {
            $record->delete();
        }

        Recombee::send(new DeleteBookmark(user()->id, $record->comic_id));
    }
}
