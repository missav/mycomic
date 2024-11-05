<?php

namespace App\Livewire;

use App\Concerns\WithPresetComics;
use App\Concerns\WithUserUuid;
use App\Models\Record;
use App\Seo;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class RecordList extends Component
{
    use WithPagination, WithUserUuid, WithPresetComics;

    public function render(): View
    {
        Seo::title(__('History'));

        $records = Record::query()
            ->with('comic.recentChapter', 'chapter')
            ->where('user_id', $this->getUserUuid())
            ->whereNotNull('chapter_id')
            ->orderByDesc('updated_at')
            ->limit(50)
            ->get();

        return view('livewire.record-list', [
            'records' => $records,
        ]);
    }
}
