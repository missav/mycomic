<?php

use App\Http\Middleware\SetLocale;
use Illuminate\Support\Facades\Route;

Route::name('zh.')->group(function () {
    require base_path('routes/app.php');
});

Route::name('cn.')->prefix('/cn')->middleware(SetLocale::class . ':cn')->group(function () {
    require base_path('routes/app.php');
});

Route::get('/sitemap.xml', \App\Http\Controllers\BaseSitemap::class)->name('sitemaps.index');
Route::get('/sitemap_pages.xml', \App\Http\Controllers\PageSitemap::class)->name('sitemaps.pages.index');
Route::get('/sitemap_{modelId}_{page}.xml', \App\Http\Controllers\ModelSitemap::class)->name('sitemaps.models.index')
    ->whereIn('modelId', array_keys(\App\Http\Controllers\BaseSitemap::modelQueries()))
    ->whereNumber('page');
