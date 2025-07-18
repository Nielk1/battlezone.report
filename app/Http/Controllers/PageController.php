<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    private static function preparePrice($pricedat) {

        return $pricedat;
    }

    public function index()
    {
        $pricedat = app(\App\Services\IsThereAnyDealService::class)->getV3PricesAndGroup();

        $prices = [];
        foreach ($pricedat['sale'] as $code => $value) {
            //$steam = false;
            //$gog = false;
            foreach ($value['deals'] as $value) {
                /*if ($value['shop']['id'] != 61) { // 61 is Steam
                    $prices[$code][$value['shop']['name']] = self::preparePrice($value);
                    //$steam = true;
                    continue;
                }
                if ($value['shop']['id'] != 35) { // 35 is GOG
                    $prices[$code][$value['shop']['name']] = self::preparePrice($value);
                    //$gog = true;
                    continue;
                }*/
                //if ($steam && $gog) break;

                $prices[$code][$value['shop']['name']] = self::preparePrice($value);
            }
        }

        return view('home', compact('pricedat', 'prices'));
    }

    public function test()
    {
        $data = app(\App\Services\IsThereAnyDealService::class)->getV3PricesAndGroup();
        return response()->json($data);
    }
}
