<?php

namespace App\Contracts;

interface RestaurantInterface {

// return meal's id, name and price by restaurant's api

public function get_meals();

// send order to restaurant's api

public function send_order();
}

