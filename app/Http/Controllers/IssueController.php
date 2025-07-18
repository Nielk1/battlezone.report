<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class IssueController extends Controller
{
    public function index()
    {
        return view('issue');
    }
}
