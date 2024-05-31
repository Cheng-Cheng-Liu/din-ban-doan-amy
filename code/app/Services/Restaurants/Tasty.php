<?php

namespace App\Services\Restaurants;

use App\Contracts\RestaurantInterface;

class Tasty implements RestaurantInterface{

public function get_meals()
{

return "tasty的meal";
}

public function send_order(){
    return "";
}
}