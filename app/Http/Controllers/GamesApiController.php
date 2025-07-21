<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GamesApiController extends Controller
{
    public function sessions()
    {
        return response()->stream(function () {
            $client = new \GuzzleHttp\Client();
            $response = $client->get('https://multiplayersessionlist.iondriver.com/api/2.0/sessions', [
                'query' => request()->query(),
                'stream' => true,
            ]);
            $body = $response->getBody();
            while (!$body->eof()) {
                echo $body->read(1024);
                ob_flush();
                flush();
            }
        });
    }
}
