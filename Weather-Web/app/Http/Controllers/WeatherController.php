<?php

namespace App\Http\Controllers;

use App\Models\WeatherHistory;
use App\Services\WeatherService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WeatherController extends Controller
{
    private $weatherService;

    public function __construct(WeatherService $weatherService)
    {
        $this->weatherService = $weatherService;
        $this->middleware('auth');
    }

    public function current(Request $request)
    {
        $user = Auth::user();
        
        // Check if coordinates are provided in the request
        $lat = $request->get('lat');
        $lon = $request->get('lon');
        $location = $request->get('location');
        
        // If no coordinates provided, check if user has saved location
        if (!$lat || !$lon) {
            $lat = $user->latitude;
            $lon = $user->longitude;
            $location = $user->current_location;
        }
        
        // If still no coordinates, return view with geolocation prompt
        if (!$lat || !$lon) {
            return view('weather.current', [
                'weather' => null,
                'alerts' => [],
                'location' => null,
                'needsLocation' => true
            ]);
        }

        $weather = $this->weatherService->getCurrentWeather($lat, $lon);
        $alerts = $this->weatherService->getWeatherAlerts($lat, $lon);

        // If no location name provided, get it from weather API response
        if (!$location && isset($weather['name'])) {
            $location = $weather['name'];
        }

        // Create a unique key for this search
        $searchKey = md5($user->id . $lat . $lon . $location);
        
        // Check if this is a new search or a page refresh
        $isNewSearch = false;
        
        // Method 1: Check if new_search parameter is present
        if ($request->query('new_search') == 1) {
            $isNewSearch = true;
        }
        // Method 2: Check if this search key is different from the last one
        elseif (session('last_weather_search') !== $searchKey) {
            $isNewSearch = true;
        }
        
        // Save to history only if it's a new search
        if ($isNewSearch) {
            WeatherHistory::create([
                'user_id' => $user->id,
                'location_name' => $location,
                'latitude' => $lat,
                'longitude' => $lon,
                'weather_data' => $weather,
                'searched_at' => now(),
            ]);
            
            // Store the current search key in session
            session(['last_weather_search' => $searchKey]);
        }

        return view('weather.current', compact('weather', 'alerts', 'location'));
    }

    public function forecast(Request $request)
    {
        $user = Auth::user();
        $lat = $request->get('lat') ?? $user->latitude ?? 6.2088;
        $lon = $request->get('lon') ?? $user->longitude ?? 106.8456;
        $location = $request->get('location', $user->current_location ?? 'Bogor');

        $forecast = $this->weatherService->getForecast($lat, $lon);

        return view('weather.forecast', compact('forecast', 'location'));
    }

    public function search(Request $request)
    {
        $query = $request->get('q');
        $locations = [];

        if ($query) {
            $locations = $this->weatherService->searchLocation($query);
        }

        return response()->json($locations);
    }

    public function history()
    {
        $user = Auth::user();
        $history = $user->weatherHistory()->orderBy('searched_at', 'desc')->paginate(20);

        return view('weather.history', compact('history'));
    }
    
    // Add this method to save user's location
    public function saveLocation(Request $request)
    {
        $user = Auth::user();
        
        $user->update([
            'latitude' => $request->lat,
            'longitude' => $request->lon,
            'current_location' => $request->location
        ]);
        
        return response()->json(['success' => true]);
    }
}