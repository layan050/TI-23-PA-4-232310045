<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class WeatherService
{
    private $apiKey;
   

    public function __construct()
    {
        $this->apiKey = config('services.openweather.api_key');
        $this->baseUrl25 = config('services.openweather.base_url_25');
        $this->baseUrl30 = config('services.openweather.base_url_30');

    }

    public function getCurrentWeather($lat, $lon)
{
    $cacheKey = "current_weather_{$lat}_{$lon}";
    
    // Log to see if we're hitting cache or making API call
    \Log::info("Weather request for: {$lat}, {$lon}");
    
    $result = Cache::remember($cacheKey, 300, function () use ($lat, $lon) {
        \Log::info("Making fresh API call to OpenWeather for {$lat}, {$lon}");
        
        $response = Http::get("{$this->baseUrl25}/weather", [
            'lat' => $lat,
            'lon' => $lon,
            'appid' => $this->apiKey,
            'units' => 'metric'
        ]);

        \Log::info("API Response Status: " . $response->status()); // Add status for debugging
        \Log::info("API Response Body: " . $response->body()); 

        
        return $response->json();
    });
    
    return $result;
}


    public function getForecast($lat, $lon)
    {   
        $cacheKey = "forecast_{$lat}_{$lon}";
        
        return Cache::remember($cacheKey, 900, function () use ($lat, $lon) {
            $response = Http::get("{$this->baseUrl25}/forecast", [
                'lat' => $lat,
                'lon' => $lon,
                'appid' => $this->apiKey,
                'units' => 'metric'
            ]);

            return $response->json();
        });
    }

    public function searchLocation($query)
    {
        $response = Http::get("http://api.openweathermap.org/geo/1.0/direct", [
            'q' => $query,
            'limit' => 5,
            'appid' => $this->apiKey
        ]);

        return $response->json();
    }

    public function getWeatherAlerts($lat, $lon)
    {
        $response = Http::get("{$this->baseUrl30}/onecall", [
            'lat' => $lat,
            'lon' => $lon,
            'appid' => $this->apiKey,
            'exclude' => 'minutely,hourly,daily'
        ]);

        $data = $response->json();
        return $data['alerts'] ?? [];
    }
}