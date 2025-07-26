<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GamesApiController extends Controller
{
    public function sessions()
    {
        $existing_query = $_SERVER['QUERY_STRING'] ?? '';
        $additional_query = http_build_query([
            //'game' => ['a', 'b'], // Example of adding multiple values for a key
            //'another_key' => 'value',
        ]);
        // todo collisions not handled

        // Rebuild the query string
        $newQueryString = $existing_query;
        if ($newQueryString) {
            $newQueryString .= '&' . $additional_query;
        } else {
            $newQueryString = $additional_query;
        }
        $url = 'https://multiplayersessionlist.iondriver.com/api/2.0/sessions' . ($newQueryString ? ('?' . $newQueryString) : '');

        return response()->stream(function () use ($url) {
            $client = new \GuzzleHttp\Client();
            $response = $client->get($url, [
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
