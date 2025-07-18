<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HelloController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\IssueController;

Route::get('/', [PageController::class, 'index'])->name('home');
Route::get('/test', [PageController::class, 'test']);

Route::get('/issue', [IssueController::class, 'index'])->name('issue');

Route::get('/welcome', function () {
    return view('welcome');
});

Route::get('/hello', [HelloController::class, 'index']);

//Route::redirect('/old-issue', '/issue');
