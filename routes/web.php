<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HelloController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\IssueController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\ChronicleController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\GamesApiController;

Route::get('/', [PageController::class, 'index'])->name('home');
Route::get('/price-cluster/{game}', [PageController::class, 'priceCluster']);
Route::get('/test', [PageController::class, 'test']);

Route::get('/issue/{type?}/{code?}', [IssueController::class, 'index'])->name('issue');
Route::get('/article/{type?}/{code?}', [ArticleController::class, 'index']);

Route::get('/chronicle/{type?}/{code?}/{chapter?}', [ChronicleController::class, 'index'])->name('chronicle');

Route::get('/images/{path}', [ImageController::class, 'show'])->where('path', '(articles|issues|chronicle)/.*\.(png|jpe?g|gif|webp)');

Route::get('/api/games/sessions', [GamesApiController::class, 'sessions']);
Route::get('/games/bz98r', [PageController::class, 'gamelist_bz98r'])->name('games_bz98r');

Route::get('/welcome', function () {
    return view('welcome');
});

Route::get('/hello', [HelloController::class, 'index']);

//Route::redirect('/old-issue', '/issue');
