@extends('layouts.app')

@section('title', 'Forecast - Weatherly')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Search Bar -->
    <div class="flex justify-center mb-8">
        <div class="relative w-full max-w-md">
            <input type="text" 
                   id="location-search"
                   placeholder="Search Location" 
                   class="search-input w-full px-4 py-3 rounded-lg text-gray-800 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-400"
                   oninput="searchLocation(this.value)">
            <button class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-blue-600 text-white p-2 rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-search"></i>
            </button>
            
            <!-- Search Results Dropdown -->
            <div id="search-results" class="hidden absolute top-full left-0 right-0 mt-1 weather-card rounded-lg max-h-60 overflow-y-auto z-10">
            </div>
        </div>
    </div>

    <!-- Forecast Title -->
    <h1 class="text-3xl font-bold text-center mb-8">7-Day Forecast for {{ $location }}</h1>

    <!-- Forecast Cards -->
    <div class="space-y-4">
        @php
            $dailyForecasts = [];
            $currentDate = '';
            
            // Group forecasts by day (OpenWeather returns 3-hour intervals)
            foreach($forecast['list'] as $item) {
                $date = date('Y-m-d', $item['dt']);
                if (!isset($dailyForecasts[$date])) {
                    $dailyForecasts[$date] = [
                        'date' => $item['dt'],
                        'temps' => [],
                        'humidity' => [],
                        'weather' => $item['weather'][0]
                    ];
                }
                $dailyForecasts[$date]['temps'][] = $item['main']['temp'];
                $dailyForecasts[$date]['humidity'][] = $item['main']['humidity'];
            }
            
            // Take only first 7 days
            $dailyForecasts = array_slice($dailyForecasts, 0, 7, true);
        @endphp

        @foreach($dailyForecasts as $date => $dayData)
            @php
                $maxTemp = round(max($dayData['temps']));
                $minTemp = round(min($dayData['temps']));
                $avgHumidity = round(array_sum($dayData['humidity']) / count($dayData['humidity']));
                $dayName = date('l', $dayData['date']);
                $dayDate = date('M j', $dayData['date']);
                
                $iconCode = $dayData['weather']['icon'] ?? '01d';
                $weatherIcon = match(substr($iconCode, 0, 2)) {
                    '01' => 'fas fa-sun',
                    '02', '03', '04' => 'fas fa-cloud',
                    '09', '10' => 'fas fa-cloud-rain',
                    '11' => 'fas fa-bolt',
                    '13' => 'fas fa-snowflake',
                    '50' => 'fas fa-smog',
                    default => 'fas fa-cloud'
                };
            @endphp
            
            <div class="weather-card rounded-lg p-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div>
                            <div class="text-xl font-semibold">{{ $dayName }}</div>
                            <div class="text-gray-300">{{ $dayDate }}</div>
                        </div>
                        <i class="{{ $weatherIcon }} text-3xl text-blue-300"></i>
                    </div>
                    
                    <div class="flex items-center space-x-8">
                        <div class="text-right">
                            <div class="text-gray-300 text-sm">Humidity</div>
                            <div class="text-lg font-semibold">{{ $avgHumidity }}%</div>
                        </div>
                        <div class="text-right">
                            <div class="text-2xl font-semibold">{{ $maxTemp }}° / {{ $minTemp }}°</div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection