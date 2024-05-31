<?php

namespace App\Services\Restaurants;
use Illuminate\Http\Request;

use App\Contracts\RestaurantInterface;

class Oishii implements RestaurantInterface{
public $id=3;

public function get_meals()
{
// Initialize a cURL session
$curl = curl_init();

// Set the URL to request
$url = "http://localhost:8082/api/test"."/89";

// Set cURL options
curl_setopt($curl, CURLOPT_URL, $url);           // The URL to fetch
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // Return the response as a string
curl_setopt($curl, CURLOPT_TIMEOUT, 30);          // Timeout in seconds

// Execute the request and fetch the response
$response = curl_exec($curl);

// Check for errors
if ($response === false) {
    $error = curl_error($curl);
    echo "cURL Error: $error";
} else {
    // Decode the JSON response
    $data = json_decode($response, true);
    print_r($data);
}

// Close the cURL session
curl_close($curl);
return "Oishii的meal";
}

public function send_order(use Illuminate\Http\Request;){
    return "";
}
}