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

        // load issue list
        $json = file_get_contents(resource_path('data/issues.json'));
        $channelsRaw = json_decode($json, true);
        $channels = array_map([Channel::class, 'fromArray'], $channelsRaw);

        return view('issue', ['channels' => $channels]);
    }
}
