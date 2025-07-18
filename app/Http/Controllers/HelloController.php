<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//use Carbon\Carbon;

class HelloController extends Controller
{
    public function index()
    {
        $message = 'hello world';
        //$time = now()->toDateTimeString();
        $time = now();
        //$time = Carbon::now()->toDateTimeString();
        return view('hello', compact('message', 'time'));
    }
}
