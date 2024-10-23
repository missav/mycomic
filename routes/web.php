<?php

use App\Http\Middleware\SetLocale;
use Illuminate\Support\Facades\Route;

Route::name('zh.')->group(function () {
    require base_path('routes/app.php');
});

Route::name('cn.')->prefix('/cn')->middleware(SetLocale::class . ':cn')->group(function () {
    require base_path('routes/app.php');
});
