<?php

namespace App\Http\Controllers;

use App\Models\TeamMember;

class PageController extends Controller
{
    private static function preparePrice($pricedat) {

        return $pricedat;
    }

    public function index()
    {
        $pricedat = app(\App\Services\IsThereAnyDealService::class)->getV3PricesAndGroup(true);

        $prices = [];
        foreach ($pricedat['sale'] as $code => $value) {
            foreach ($value['deals'] as $value) {
                $prices[$code][$value['shop']['name']] = self::preparePrice($value);
            }
        }

        return view('home', compact('prices'));
    }

    public function priceCluster($game)
    {
        $pricedat = app(\App\Services\IsThereAnyDealService::class)->getV3PricesAndGroup(false);

        $deal = null;
        foreach ($pricedat['sale'] as $code => $value) {
            if ($code !== $game) continue;
            $deal = [];
            foreach ($value['deals'] as $value) {
                $deal[$value['shop']['name']] = self::preparePrice($value);
            }
            break;
        }
        $code = $game;

        return view('partials.price-cluster', compact('code', 'deal'));
    }

    public function modding()
    {
        return view('modding');
    }

    public function social()
    {
        return view('social');
    }

    public function about()
    {
        $filename = resource_path("data/team.json");
        $sdata = json_decode(file_get_contents($filename), true);
        $team = [];
        if (is_array($sdata)) {
            foreach ($sdata as $member) {
                $team[] = TeamMember::fromArray($member);
            }
        }
        return view('about', ['team' => $team]);
    }

    public function gamelist()
    {
        return view('games');
    }

    public function gamelist_bz98r()
    {
        return view('games.bz98r');
    }

    public function gamelist_bzcc()
    {
        return view('games.bzcc');
    }
}
