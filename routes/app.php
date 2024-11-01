<?php

Route::get('/', \App\Livewire\Home::class)->name('home');
Route::get('/comics', \App\Livewire\ComicList::class)->name('comics.index');
Route::get('/comics/{comic}', \App\Livewire\ComicDetail::class)->name('comics.view');
Route::get('/chapters/{chapter}', \App\Livewire\ChapterReader::class)->name('chapters.view');
Route::get('/records', \App\Livewire\RecordList::class)->name('records.index');
