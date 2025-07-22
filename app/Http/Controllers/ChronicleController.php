<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Channel;
use App\Models\Issue;
use App\Models\Article;

class ChronicleController extends Controller
{
    public function index($type = null, $code = null, $chapter = null)
    {
        $json = file_get_contents(resource_path('data/chronicles.json'));

        $channelsRaw = json_decode($json, true);
        if (!is_array($channelsRaw)) {
            abort(404);
        }
        $channels = array_map([Channel::class, 'fromArray'], $channelsRaw);

        if (!preg_match('/^[a-zA-Z0-9_-]+$/', $type ?? ''))
            $type = null; // reset type if type is invalid

        if (!$code || !preg_match('/^[a-zA-Z0-9_-]+$/', $code ?? '')) {
            // get earliest code of all types
            $earliestEntry = $channels[0];

            // get earliest code of specific type if possible
            if ($type) {
                for ($i = 0; $i < count($channels); $i++) {
                    if ($type === null || $channels[$i]->type === $type) {
                        $earliestEntry = $channels[$i];
                        break;
                    }
                }
            }

            return redirect()->route('chronicle', ['type' => $earliestEntry->type, 'code' => $earliestEntry->code], 302);
        }

        $issue = null;
        $filename = resource_path("data/chronicle/$type/$code.json");
        if (file_exists($filename)) {
            $issuedata = json_decode(file_get_contents($filename), true);

            if (!is_array($issuedata)) {
                abort(404);
            }

            $issue = Issue::fromArray($issuedata);

            $foundAlready = false;

            if (!$chapter) {
                $filename1 = resource_path("data/chronicle/$type/$code.shtml");
                $filename2 = resource_path("data/chronicle/$type/$code.json");
                if (file_exists($filename1) && file_exists($filename2)) {
                    $foundAlready = true;
                }
            }

            if (!$foundAlready) {
                if (count($issuedata['articles'] ?? []) == 0) {
                    abort(404);
                }

                if (!$chapter) {
                    $chapter = $issue->articles[0]->code;
                    return redirect()->route('chronicle', ['type' => $type, 'code' => $code, 'chapter' => $chapter], 302);
                }
            }

            $html = null;
            if (!$foundAlready) {
                $filename1 = resource_path("data/chronicle/$type/$code/$chapter.shtml");
                $filename2 = resource_path("data/chronicle/$type/$code/$chapter.json");
            }
            if (file_exists($filename1) && file_exists($filename2)) {
                $html = file_get_contents($filename1);

                // Replace SVG logo placeholders like <!--#svg LOGO_NAME -->
                $html = preg_replace_callback('/<!--#svg ([a-zA-Z0-9_-]+) -->/', function($matches) {
                    $svgName = $matches[1];
                    $svgPath = resource_path("svg/{$svgName}.svg");
                    return file_exists($svgPath) ? file_get_contents($svgPath) : '';
                }, $html);

                $sdata = json_decode(file_get_contents($filename2), true);
                if (!is_array($sdata)) {
                    abort(404);
                }
                $article = Article::fromArray($sdata);
            } else {
                abort(404);
            }
        }

        return view('chronicle', ['activeNav' => 'chronicle', 'channels_header' => 'Chronicles', 'channels' => $channels, 'article' => $article, 'type' => $type, 'code' => $code, 'subcode' => $chapter, 'issue' => $issue, 'content' => $html]);
    }
}
