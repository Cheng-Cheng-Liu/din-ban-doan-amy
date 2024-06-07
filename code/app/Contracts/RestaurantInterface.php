<?php

namespace App\Contracts;

interface RestaurantInterface {

// return meal's id, name and price by restaurant's api

public function get_meals();

// send order to restaurant's api

public function send_order($user_name,$phone,$restaurant_id,$amount,$status,$remark,$pick_up_time,$created_time,$detail,$uuid);
}

