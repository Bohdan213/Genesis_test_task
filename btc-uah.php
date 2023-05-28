<?php

function getBitcoinUAHValue() {
    $url = "https://api.coingecko.com/api/v3/simple/price";
    
    $params = [
        "ids" => "bitcoin",
        "vs_currencies" => "uah",
    ];
    
    $url .= "?" . http_build_query($params);
    
    $response = file_get_contents($url);
    $data = json_decode($response, true);
    
    $btc_uah_value = $data["bitcoin"]["uah"];
    
    return $btc_uah_value;
}


?>
