<?php

use App\Http\Middleware\SetLocale;
use Illuminate\Support\Facades\Route;

Route::name('zh.')->group(function () {
    require base_path('routes/app.php');
});

Route::name('cn.')->prefix('/cn')->middleware(SetLocale::class . ':cn')->group(function () {
    require base_path('routes/app.php');
});

Route::post('/comics/{comic}', \App\Http\Controllers\SyncComic::class)->name('comics.sync');
Route::post('/comics/{comic}/bookmark', \App\Http\Controllers\BookmarkComic::class)->name('comics.bookmark');
Route::delete('/comics/{comic}/bookmark', \App\Http\Controllers\UnbookmarkComic::class)->name('comics.unbookmark');
Route::post('/comics/{comic}/review', \App\Http\Controllers\ReviewComic::class)->name('comics.review');
Route::post('/logout', \App\Http\Controllers\Logout::class)->name('logout');

Route::post('/chapters/{chapter}', \App\Http\Controllers\SyncChapter::class)->name('chapters.sync');

Route::get('/sitemap.xml', \App\Http\Controllers\BaseSitemap::class)->name('sitemaps.index');
Route::get('/sitemap_pages.xml', \App\Http\Controllers\PageSitemap::class)->name('sitemaps.pages.index');
Route::get('/sitemap_{modelId}_{page}.xml', \App\Http\Controllers\ModelSitemap::class)->name('sitemaps.models.index')
    ->whereIn('modelId', array_keys(\App\Http\Controllers\BaseSitemap::modelQueries()))
    ->whereNumber('page');
