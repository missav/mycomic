<?php

namespace App\Http\Controllers;

use App\Concerns\WithPresetComics;
use App\Concerns\WithUserUuid;
use App\Models\Record;
use App\Seo;
use Illuminate\Contracts\View\View;

class BookmarkList
{
    use WithUserUuid, WithPresetComics;

    public function __invoke(): View
    {
        Seo::title(__('My bookmarks'));

        $records = Record::query()
            ->with('comic.recentChapter', 'chapter')
            ->where('user_id', $this->getUserUuid())
            ->where('has_bookmarked', 1)
            ->orderByDesc('updated_at')
            ->limit(50)
            ->get();

        return view('record-list', [
            'records' => $records,
            'recentUpdatedComics' => $this->recentUpdatedComics(),
            'recentPublishedComics' => $this->recentPublishedComics(),
        ]);
    }
}
