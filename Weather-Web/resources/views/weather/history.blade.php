@extends('layouts.app')

@section('title', 'Weather History - Weatherly')

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

    <!-- History Title -->
    <h1 class="text-3xl font-bold text-center mb-8">Search History</h1>

    <!-- History Cards -->
    @if($history->count() > 0)
        <div class="space-y-4">
            @foreach($history as $record)
                @php
                    $weatherData = $record->weather_data;
                    $iconCode = $weatherData['weather'][0]['icon'] ?? '01d';
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
                
                <div class="weather-card rounded-lg p-6 hover:bg-opacity-20 transition-all cursor-pointer"
                     onclick="viewWeather('{{ $record->latitude }}', '{{ $record->longitude }}', '{{ $record->location_name }}')">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <div>
                                <div class="text-xl font-semibold">{{ $record->location_name }}</div>
                                <div class="text-gray-300 text-sm">{{ $record->searched_at->format('M j, Y - g:i A') }}</div>
                            </div>
                            <i class="{{ $weatherIcon }} text-3xl text-blue-300"></i>
                        </div>
                        
                        <div class="flex items-center space-x-8">
                            <div class="text-right">
                                <div class="text-gray-300 text-sm">Humidity</div>
                                <div class="text-lg font-semibold">{{ $weatherData['main']['humidity'] }}%</div>
                            </div>
                            <div class="text-right">
                                <div class="text-2xl font-semibold">{{ round($weatherData['main']['temp']) }}°</div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $history->links() }}
        </div>
    @else
        <div class="weather-card rounded-lg p-8 text-center">
            <i class="fas fa-history text-4xl text-gray-400 mb-4"></i>
            <h3 class="text-xl font-semibold mb-2">No Search History</h3>
            <p class="text-gray-300">Start searching for locations to see your history here.</p>
        </div>
    @endif
</div>

@push('scripts')
<script>
function viewWeather(lat, lon, location) {
    const url = new URL('{{ route("weather.current") }}');
    url.searchParams.set('lat', lat);
    url.searchParams.set('lon', lon);
    url.searchParams.set('location', location);
    url.searchParams.set('new_search', 1); // Required flag to save to DB
    window.location.href = url.toString();
}
</script>
@endpush
@endsection