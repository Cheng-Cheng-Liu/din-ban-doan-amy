<?php

namespace App\Contracts;

interface RestaurantInterface {

// return meal's id, name and price by restaurant's api

public function getMealsByApi();

// send order to restaurant's api

public function sendOrder(array $order);


}

