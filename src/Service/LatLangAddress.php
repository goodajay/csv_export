<?php

namespace App\Service;

class LatLangAddress
{
    public static function get_lat_lang($address = '')
    {
        $prepAddr = str_replace(' ','+',$address);
        $geocode=file_get_contents('https://maps.google.com/maps/api/geocode/json?address='.$prepAddr.'&key=AIzaSyDfpByr2GQO-wJIe786uY4USZ2fCNK0j64');
        $output= json_decode($geocode);
        if($output->results){
            return [
                'latitude' => $output->results[0]->geometry->location->lat,
        	   'longitude' => $output->results[0]->geometry->location->lng,
            ];
        } else {
            // dump($output);
            // dump($address);
            return [
            	'latitude' => '',
            	'longitude' => '',
            ];
        }
    }
}