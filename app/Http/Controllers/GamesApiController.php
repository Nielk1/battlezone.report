<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GamesApiController extends Controller
{
    public function sessions()
    {
        $existing_query = $_SERVER['QUERY_STRING'] ?? '';
        $isChunked = false;
        $newQueryString = $existing_query;

        // Detect and handle mode=chunked or other mode values
        if (preg_match('/(?:^|&)(mode)=([^&]*)/', $existing_query, $matches)) {
            if ($matches[2] === 'chunked') {
                $isChunked = true;
            } else {
                // Remove all mode=... occurrences
                $newQueryString = preg_replace('/(&?mode=[^&]*)/', '', $existing_query);
                // Add mode=event
                $newQueryString = ltrim($newQueryString, '&');
                $newQueryString .= ($newQueryString ? '&' : '') . 'mode=event';
            }
        }

        $additional_query = http_build_query([
            // Add additional query params here if needed
        ]);
        // todo collisions not handled

        if ($additional_query) {
            $newQueryString .= ($newQueryString ? '&' : '') . $additional_query;
        }

        //$url = 'https://multiplayersessionlist.iondriver.com/api/2.0/sessions' . ($newQueryString ? ('?' . $newQueryString) : '');
        $url = 'http://localhost:6000/api/2.0/sessions' . ($newQueryString ? ('?' . $newQueryString) : '');

        if ($isChunked) {
            // Keep current chunked proxy logic
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
        } else {
            // Proxy as SSE event stream, pass through as-is
            return response()->stream(function () use ($url) {
                $client = new \GuzzleHttp\Client();
                $response = $client->get($url, [
                    'stream' => true,
                ]);
                $body = $response->getBody();
                while (!$body->eof()) {
                    $chunk = $body->read(1024);
                    if ($chunk === false || $chunk === '') {
                        usleep(10000); // 10ms
                        continue;
                    }
                    echo $chunk;
                    ob_flush();
                    flush();
                }
            }, 200, [
                'Content-Type' => 'text/event-stream',
                'Cache-Control' => 'no-cache',
                'Connection' => 'keep-alive',
            ]);
        }
    }
}
