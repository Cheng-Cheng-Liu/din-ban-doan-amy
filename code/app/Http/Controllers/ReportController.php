<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use App\Models\Restaurant;

class ReportController extends Controller
{
    //
    public function restaurantOrderAmount(Request $request){
        $date=$request->input('date');
        $restaurants=Restaurant::where("status","=",1)->get()->toArray();
        $result=[];
        $i=0;
        foreach($restaurants as $restaurants){
           
        $key=$restaurants['id'].$date;
        $value=Redis::zrange($key, 0, -1, ['withscores' => true]);
        print_r($value);
        $result[$i][] = Redis::zrange($key, 0, -1, ['withscores' => true]);

        $i++;

    }
    return $result;
    }
}
