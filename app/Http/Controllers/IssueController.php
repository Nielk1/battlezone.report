<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Channel;

class IssueController extends Controller
{
    public function index($type = null, $id = null)
    {
        // if type is null, redirect to default type
        // if id is null, redirect to latest subpage of that type

        //if (!$id) {
        //    // Replace 'latest-id' with logic to get your latest subpage ID
        //    $latestId = $this->getLatestIssueId();
        //    return redirect()->route('issue', ['id' => $latestId]);
        //}
        //return view('issue');

        $json = file_get_contents(resource_path('data/issues.json'));
        $channelsRaw = json_decode($json, true);
        $channels = array_map([Channel::class, 'fromArray'], $channelsRaw);

        if (!$id) {
            // get latest id of all types
            $latestEntry = $channels[count($channels) - 1];

            // get latest id of specific type if possible
            if ($type) {
                for ($i = count($channels) - 1; $i >= 0; $i--) {
                    if ($type === null || $channels[$i]->type === $type) {
                        $latestEntry = $channels[$i];
                        break;
                    }
                }
            }

            return redirect()->route('issue', ['type' => $latestEntry->type, 'id' => $latestEntry->action], 302);
        }

        return view('issue', ['channels' => $channels]);
    }
}
