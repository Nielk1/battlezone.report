<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;

class ArticleController extends Controller
{
    public function index($type = null, $code = null)
    {
        // Only allow safe characters in $type and $code
        if (
            !preg_match('/^[a-zA-Z0-9_-]+$/', $type ?? '') ||
            !preg_match('/^[a-zA-Z0-9_-]+$/', $code ?? '')
        ) {
            abort(404);
        }

        $filename1 = resource_path("data/articles/$type/$code.shtml");
        $filename2 = resource_path("data/articles/$type/$code.json");
        if (file_exists($filename1) && file_exists($filename2)) {
            $html = file_get_contents($filename1);

            // Replace SVG logo placeholders like <!--#svg LOGO_NAME -->
            $html = preg_replace_callback('/<!--#svg ([a-zA-Z0-9_-]+) -->/', function($matches) {
                $svgName = $matches[1];
                //$svgPath = resource_path("svg/{$svgName}.svg");
                //return file_exists($svgPath) ? file_get_contents($svgPath) : '';
                return '<svg width="24" height="24"><use xlink:href="#svg/' . $svgName . '"></use></svg>';
            }, $html);

            $sdata = json_decode(file_get_contents($filename2), true);
            $article = Article::fromArray($sdata);
            return view('article', ['content' => $html, 'article' => $article, 'type' => $type, 'code' => $code]);
        }

        abort(404);
    }
}
