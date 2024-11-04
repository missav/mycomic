<?php

Route::get('/', \App\Livewire\Home::class)->name('home');
Route::get('/comics', \App\Livewire\ComicList::class)->name('comics.index');
Route::get('/rank', \App\Livewire\RankList::class)->name('rank');
Route::get('/comics/{comic}', \App\Livewire\ComicDetail::class)->name('comics.view');
Route::get('/chapters/{chapter}', \App\Livewire\ChapterReader::class)->name('chapters.view');
Route::get('/bookmarks', \App\Livewire\BookmarkList::class)->name('bookmarks.index');
Route::get('/records', \App\Livewire\RecordList::class)->name('records.index');
