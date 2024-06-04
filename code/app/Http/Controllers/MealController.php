<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Contracts\RestaurantInterface;
use App\Models\Meal;


class MealController extends Controller
{
    function saveMeal(RestaurantInterface $restaurant){
        $restaurant->get_meals();
}
}