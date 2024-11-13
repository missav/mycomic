<?php

namespace App\Http\Controllers;

use App\Concerns\WithPresetComics;
use App\Concerns\WithUserUuid;
use App\Models\Record;
use App\Seo;
use Illuminate\Contracts\View\View;

class RecordList
{
    use WithUserUuid, WithPresetComics;

    public function __invoke(): View
    {
        Seo::title(__('History'));

        if ($userUuid = $this->getUserUuid()) {
            $records = Record::query()
                ->with('comic.recentChapter', 'chapter')
                ->where('user_id', $userUuid)
                ->whereNotNull('chapter_id')
                ->orderByDesc('updated_at')
                ->limit(50)
                ->get();
        } else {
            $records = collect();
        }

        return view('record-list', [
            'records' => $records,
            'recentUpdatedComics' => $this->recentUpdatedComics(),
            'recentPublishedComics' => $this->recentPublishedComics(),
        ]);
    }
}
