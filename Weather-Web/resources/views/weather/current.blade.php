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

    <!-- Location Permission Request -->
    @if(isset($needsLocation) && $needsLocation)
        <div class="weather-card rounded-2xl p-8 mb-6 text-center">
            <div class="mb-4">
                <i class="fas fa-map-marker-alt text-6xl text-blue-300 mb-4"></i>
                <h2 class="text-2xl font-semibold mb-2">Enable Location Access</h2>
                <p class="text-gray-300 mb-6">To get accurate weather data for your current location, please allow location access.</p>
                <button onclick="getCurrentLocation()" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-crosshairs mr-2"></i>
                    Get My Location
                </button>
            </div>
        </div>
    @else
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
    @endif
</div>

<script>
let debounceTimer;

function searchLocation(query) {
    clearTimeout(debounceTimer);
    
    if (query.length < 2) {
        hideSearchResults();
        return;
    }
    
    debounceTimer = setTimeout(() => {
        fetch(`/search?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                showSearchResults(data);
            })
            .catch(error => {
                console.error('Error:', error);
                hideSearchResults();
            });
    }, 300);
}

function showSearchResults(locations) {
    const resultsDiv = document.getElementById('search-results');
    
    if (locations.length === 0) {
        hideSearchResults();
        return;
    }
    
    let html = '';
    locations.forEach(location => {
        html += `
            <div class="p-3 hover:bg-gray-700 cursor-pointer border-b border-gray-600 last:border-b-0"
                 onclick="selectLocation('${location.name}', ${location.lat}, ${location.lon}, '${location.country}')">
                <div class="font-medium">${location.name}</div>
                <div class="text-sm text-gray-400">${location.country}</div>
            </div>
        `;
    });
    
    resultsDiv.innerHTML = html;
    resultsDiv.classList.remove('hidden');
}

function hideSearchResults() {
    document.getElementById('search-results').classList.add('hidden');
}

function selectLocation(name, lat, lon, country) {
    const locationName = country ? `${name}, ${country}` : name;
    window.location.href = `/current?lat=${lat}&lon=${lon}&location=${encodeURIComponent(locationName)}&new_search=1`;
}

function getCurrentLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function(position) {
                const lat = position.coords.latitude;
                const lon = position.coords.longitude;
                
                // Get location name from coordinates using reverse geocoding
                fetch(`https://api.openweathermap.org/geo/1.0/reverse?lat=${lat}&lon=${lon}&limit=1&appid=f1bc4e0285f72ac7d9bddfd3679b14c1`)
                    .then(response => response.json())
                    .then(data => {
                        let locationName = 'Your Location';
                        if (data.length > 0) {
                            locationName = data[0].name;
                            if (data[0].country) {
                                locationName += `, ${data[0].country}`;
                            }
                        }
                        
                        // Save user's location for future use
                        fetch('/save-location', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                lat: lat,
                                lon: lon,
                                location: locationName
                            })
                        });
                        
                        // Redirect to current weather with coordinates
                        window.location.href = `/current?lat=${lat}&lon=${lon}&location=${encodeURIComponent(locationName)}&new_search=1`;
                    })
                    .catch(error => {
                        console.error('Error getting location name:', error);
                        // Still redirect with coordinates even if we can't get the name
                        window.location.href = `/current?lat=${lat}&lon=${lon}&location=Your%20Location&new_search=1`;
                    });
            },
            function(error) {
                let errorMessage = 'Unable to get your location. ';
                switch(error.code) {
                    case error.PERMISSION_DENIED:
                        errorMessage += 'Location access denied by user.';
                        break;
                    case error.POSITION_UNAVAILABLE:
                        errorMessage += 'Location information is unavailable.';
                        break;
                    case error.TIMEOUT:
                        errorMessage += 'Location request timed out.';
                        break;
                    default:
                        errorMessage += 'An unknown error occurred.';
                        break;
                }
                alert(errorMessage);
            }
        );
    } else {
        alert('Geolocation is not supported by this browser.');
    }
}

// Hide search results when clicking outside
document.addEventListener('click', function(event) {
    const searchInput = document.getElementById('location-search');
    const searchResults = document.getElementById('search-results');
    
    if (!searchInput.contains(event.target) && !searchResults.contains(event.target)) {
        hideSearchResults();
    }
});
</script>
@endsection