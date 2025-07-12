<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Weatherly')</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            min-height: 100vh;
            font-family: 'Inter', sans-serif;
        }
        .weather-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .search-input {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
        }
        .nav-link.active {
            color: #60a5fa !important;
            border-bottom: 2px solid #60a5fa;
        }
    </style>
</head>
<body class="text-white">
    <!-- Navigation Header -->
    <nav class="flex justify-between items-center p-6">
        <div class="flex items-center space-x-2">
            <i class="fas fa-cloud text-2xl"></i>
            <span class="text-xl font-bold">Weatherly</span>
        </div>
        
        <div class="flex items-center space-x-8">
            <a href="{{ route('weather.current') }}" 
               class="nav-link hover:text-blue-300 transition-colors {{ request()->routeIs('weather.current') ? 'active' : '' }}">
                Current
            </a>
            <a href="{{ route('weather.forecast') }}" 
               class="nav-link hover:text-blue-300 transition-colors {{ request()->routeIs('weather.forecast') ? 'active' : '' }}">
                Forecast
            </a>
            <a href="{{ route('weather.history') }}" 
               class="nav-link hover:text-blue-300 transition-colors {{ request()->routeIs('weather.history') ? 'active' : '' }}">
                History
            </a>
            <a href="{{ route('nicknames.index') }}" 
               class="nav-link hover:text-blue-300 transition-colors {{ request()->routeIs('nicknames.index') ? 'active' : '' }}">
                Nicknames
            </a>
            
            @auth
                <div class="flex items-center space-x-4">
                    <span class="text-sm">{{ Auth::user()->name }}</span>
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-sm hover:text-blue-300 transition-colors">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </button>
                    </form>
                </div>
            @endauth
        </div>
    </nav>

    <!-- Main Content -->
    <main class="container mx-auto px-6">
        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        // Set up CSRF token for axios
        axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Search functionality
        function searchLocation(query) {
            if (query.length < 2) return;
            
            axios.get('/search', { params: { q: query } })
                .then(response => {
                    displaySearchResults(response.data);
                })
                .catch(error => {
                    console.error('Search error:', error);
                });
        }
        
        function displaySearchResults(locations) {
            const resultsContainer = document.getElementById('search-results');
            if (!resultsContainer) return;
            
            resultsContainer.innerHTML = '';
            
            locations.forEach(location => {
                const div = document.createElement('div');
                div.className = 'p-3 hover:bg-white hover:bg-opacity-20 cursor-pointer border-b border-white border-opacity-20';
                div.innerHTML = `
                    <div class="font-medium">${location.name}</div>
                    <div class="text-sm text-gray-300">${location.country}</div>
                `;
                div.onclick = () => selectLocation(location);
                resultsContainer.appendChild(div);
            });
            
            resultsContainer.classList.remove('hidden');
        }
        
        function selectLocation(location) {
            const currentUrl = new URL(window.location);
            currentUrl.searchParams.set('lat', location.lat);
            currentUrl.searchParams.set('lon', location.lon);
            currentUrl.searchParams.set('location', location.name);
            window.location.href = currentUrl.toString();
        }
    </script>
    
    @stack('scripts')
</body>
</html>