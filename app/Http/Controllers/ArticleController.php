<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index($type = null, $id = null)
    {
        return response()->json(['error' => 'Not Found'], 404);
    }
}
