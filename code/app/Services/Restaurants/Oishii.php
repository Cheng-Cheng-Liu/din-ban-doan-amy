<?php

namespace App\Services\Restaurants;

use App\Contracts\RestaurantInterface;

class Oishii implements RestaurantInterface{

public function get_meals()
{

return "Oishii的meal";
}

public function send_order(){
    return "";
}
}