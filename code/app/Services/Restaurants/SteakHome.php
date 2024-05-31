<?php

namespace App\Services\Restaurants;

use App\Contracts\RestaurantInterface;

class SteakHome implements RestaurantInterface{

public function get_meals()
{

return "steakhome的meal";
}

public function send_order(){
    return "";
}
}