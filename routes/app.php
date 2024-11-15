<?php

Route::get('/', \App\Http\Controllers\Home::class)->name('home');
Route::get('/comics', \App\Http\Controllers\ComicList::class)->name('comics.index');
Route::get('/rank', \App\Http\Controllers\RankList::class)->name('rank');
Route::get('/comics/{comic}', \App\Http\Controllers\ComicDetail::class)->name('comics.view');
Route::get('/chapters/{chapter}', \App\Http\Controllers\ChapterReader::class)->name('chapters.view');
Route::get('/bookmarks', \App\Http\Controllers\BookmarkList::class)->name('bookmarks.index');
Route::get('/records', \App\Http\Controllers\RecordList::class)->name('records.index');

Route::post('/register', \App\Http\Controllers\Register::class)->name('register')->middleware('guest');
Route::post('/login', \App\Http\Controllers\Login::class)->name('login')->middleware('guest');
Route::post('/forget', \App\Http\Controllers\ForgetPassword::class)->name('forget')->middleware('guest');
