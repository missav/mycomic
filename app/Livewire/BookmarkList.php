<?php

namespace App\Livewire;

use App\Concerns\WithSidebar;
use App\Concerns\WithUserUuid;
use App\Models\Record;
use App\Seo;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class BookmarkList extends Component
{
    use WithPagination, WithUserUuid, WithSidebar;

    public function render(): View
    {
        Seo::title(__('My bookmarks'));

        $records = Record::query()
            ->with('comic.recentChapter', 'chapter')
            ->where('user_id', $this->getUserUuid())
            ->where('has_bookmarked', 1)
            ->orderByDesc('updated_at')
            ->limit(50)
            ->get();

        return view('livewire.record-list', [
            'records' => $records,
        ]);
    }
}
