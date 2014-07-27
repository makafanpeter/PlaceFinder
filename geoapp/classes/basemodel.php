<?php

abstract class BaseModel {

    const GOOGLE_BASE_API = 'https://maps.googleapis.com/maps/api/';
    const GOOGLE_PLACE_API = 'place/';
    const GOOGLE_GEOCODE_API = 'geocode/';
    const GOOGLE_DIRECTIONS_API = 'directions/';
    const GOOGLE_API_KEY = "AIzaSyBOirZtEdVTP0AnOwFT-WmMXgqYTktoasE";

    protected function callAPI($method, $url, $data = false) {
        $curl = curl_init();

        switch ($method) {
            case "POST":
                curl_setopt($curl, CURLOPT_POST, 1);

                if (!empty($data))
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                break;
            case "PUT":
                curl_setopt($curl, CURLOPT_PUT, 1);
                break;
            default:
                if (!empty($data)) {
                    $url .= '?' . $data;
                }
        }

        //error_log("URL: $url");
        // set Curl options here
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        
        // Make the call and close connection afterwards
        $result = curl_exec($curl);
        curl_close($curl);

        return $result;
    }

}
