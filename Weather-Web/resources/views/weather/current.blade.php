@extends('layouts.app')

@section('title', 'Current Weather - Weatherly')

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

    <!-- Current Weather Card -->
    <div class="weather-card rounded-2xl p-8 mb-6 text-center">
        <div class="flex items-center justify-center mb-4">
            <i class="fas fa-map-marker-alt mr-2"></i>
            <h2 class="text-2xl font-semibold">{{ $location }}</h2>
        </div>
        
        <div class="flex items-center justify-center mb-6">
            @php
                $iconCode = $weather['weather'][0]['icon'] ?? '01d';
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
            <i class="{{ $weatherIcon }} text-6xl mr-4 text-blue-300"></i>
            <span class="text-8xl font-light">{{ round($weather['main']['temp']) }}°</span>
        </div>
        
        <div class="grid grid-cols-2 gap-8 max-w-md mx-auto">
            <div>
                <div class="text-gray-300 mb-1">Humidity</div>
                <div class="text-2xl font-semibold">{{ $weather['main']['humidity'] }}%</div>
            </div>
            <div>
                <div class="text-gray-300 mb-1">Wind Speed</div>
                <div class="text-2xl font-semibold">{{ round($weather['wind']['speed'] * 3.6) }} km/h</div>
            </div>
        </div>
    </div>

    <!-- Weather Alerts -->
    @if(empty($alerts))
        <div class="bg-green-600 bg-opacity-80 rounded-lg p-4 text-center">
            <i class="fas fa-check-circle mr-2"></i>
            Today is safe - No weather alerts in your area
        </div>
    @else
        @foreach($alerts as $alert)
            <div class="bg-red-600 bg-opacity-80 rounded-lg p-4 mb-4">
                <div class="flex items-center mb-2">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <h3 class="font-semibold">{{ $alert['event'] }}</h3>
                </div>
                <p class="text-sm">{{ $alert['description'] }}</p>
            </div>
        @endforeach
    @endif
</div>
@endsection