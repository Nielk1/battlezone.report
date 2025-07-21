<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Channel;
use App\Models\Issue;
use App\Models\Article;

class IssueController extends Controller
{
    public function index($type = null, $code = null)
    {
        $json = file_get_contents(resource_path('data/issues.json'));
        $channelsRaw = json_decode($json, true);
        if (!is_array($channelsRaw)) {
            abort(404);
        }
        $channels = array_map([Channel::class, 'fromArray'], $channelsRaw);

        if (!preg_match('/^[a-zA-Z0-9_-]+$/', $type ?? ''))
            $type = null; // reset type if type is invalid

        if (!$code || !preg_match('/^[a-zA-Z0-9_-]+$/', $code ?? '')) {
            // get latest code of all types
            $latestEntry = $channels[count($channels) - 1];

            // get latest code of specific type if possible
            if ($type) {
                for ($i = count($channels) - 1; $i >= 0; $i--) {
                    if ($type === null || $channels[$i]->type === $type) {
                        $latestEntry = $channels[$i];
                        break;
                    }
                }
            }

            return redirect()->route('issue', ['type' => $latestEntry->type, 'code' => $latestEntry->code], 302);
        }

        $articles = [];
        $issue = null;
        $filename = resource_path("data/issues/$type/$code.json");
        if (file_exists($filename)) {
            $issuedata = json_decode(file_get_contents($filename), true);
            if (!is_array($issuedata)) {
                abort(404);
            }
            $issue = Issue::fromArray($issuedata);

            foreach ($issue->articles as $art) {
                $filename1 = resource_path("data/articles/$art->type/$art->code.shtml");
                $filename2 = resource_path("data/articles/$art->type/$art->code.json");
                if (file_exists($filename1) && file_exists($filename2)) {
                    $html = file_get_contents($filename1);
                    $sdata = json_decode(file_get_contents($filename2), true);
                    if (!is_array($sdata)) {
                        continue; // skip invalid article
                    }
                    $article = Article::fromArray($sdata);

                    $articles[] = [
                        'content' => $html,
                        'article' => $article,
                        'type' => $art->type,
                        'code' => $art->code
                    ];
                }
            }
        }

        return view('issue', ['channels_header' => 'Issues', 'channels' => $channels, 'articles' => $articles, 'type' => $type, 'code' => $code, 'issue' => $issue]);
    }
}
