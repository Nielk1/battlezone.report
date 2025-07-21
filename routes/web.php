<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HelloController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\IssueController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\ImageController;

Route::get('/', [PageController::class, 'index'])->name('home');
Route::get('/test', [PageController::class, 'test']);

Route::get('/issue/{type?}/{code?}', [IssueController::class, 'index'])->name('issue');
Route::get('/article/{type?}/{code?}', [ArticleController::class, 'index']);

Route::get('/images/{path}', [ImageController::class, 'show'])->where('path', '(articles|issues)/.*\.(png|jpe?g|gif|webp)');

Route::get('/welcome', function () {
    return view('welcome');
});

Route::get('/hello', [HelloController::class, 'index']);

//Route::redirect('/old-issue', '/issue');
