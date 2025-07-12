@extends('layouts.app')

@section('title', 'Nickname Locations - Weatherly')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Page Title -->
    <h1 class="text-3xl font-bold text-center mb-8">Nickname Locations</h1>

    <!-- Add New Nickname Form -->
    <div class="weather-card rounded-lg p-6 mb-8">
        <h2 class="text-xl font-semibold mb-4">Add New Nickname Location</h2>
        
        <!-- Search for Location -->
        <div class="relative mb-4">
            <input type="text" 
                   id="location-search"
                   placeholder="Search for a location to nickname" 
                   class="search-input w-full px-4 py-3 rounded-lg text-gray-800 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-400"
                   oninput="searchLocation(this.value)">
            
            <!-- Search Results Dropdown -->
            <div id="search-results" class="hidden absolute top-full left-0 right-0 mt-1 weather-card rounded-lg max-h-60 overflow-y-auto z-10">
            </div>
        </div>

        <!-- Nickname Form -->
        <form id="nickname-form" action="{{ route('nicknames.store') }}" method="POST" class="hidden">
            @csrf
            <input type="hidden" id="original_name" name="original_name">
            <input type="hidden" id="latitude" name="latitude">
            <input type="hidden" id="longitude" name="longitude">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-2">Location</label>
                    <input type="text" id="selected_location" class="w-full px-4 py-2 rounded-lg bg-gray-700 text-white" readonly>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Nickname</label>
                    <input type="text" name="nickname" placeholder="e.g., Home, Work, Mom's House" 
                           class="w-full px-4 py-2 rounded-lg bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                </div>
            </div>
            
            <div class="flex justify-end mt-4">
                <button type="button" onclick="cancelNickname()" class="mr-4 px-4 py-2 text-gray-300 hover:text-white">Cancel</button>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Save Nickname
                </button>
            </div>
        </form>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="bg-green-600 bg-opacity-80 rounded-lg p-4 mb-6">
            <i class="fas fa-check-circle mr-2"></i>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-600 bg-opacity-80 rounded-lg p-4 mb-6">
            <i class="fas fa-exclamation-triangle mr-2"></i>
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <!-- Nickname Locations List -->
    @if($nicknames->count() > 0)
        <div class="space-y-4">
            @foreach($nicknames as $nickname)
                <div class="weather-card rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <div class="flex items-center space-x-4">
                                <div>
                                    <h3 class="text-xl font-semibold">{{ $nickname->nickname }}</h3>
                                    <p class="text-gray-300">{{ $nickname->original_name }}</p>
                                    <p class="text-sm text-gray-400">Added {{ $nickname->created_at->format('M j, Y') }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex items-center space-x-4">
                            <!-- View Weather Button -->
                            <button onclick="viewNicknameWeather('{{ $nickname->latitude }}', '{{ $nickname->longitude }}', '{{ $nickname->nickname }}')"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                <i class="fas fa-eye mr-2"></i>View Weather
                            </button>
                            
                            <!-- Edit Button -->
                            <button onclick="editNickname({{ $nickname->id }}, '{{ $nickname->nickname }}')"
                                    class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors">
                                <i class="fas fa-edit"></i>
                            </button>
                            
                            <!-- Delete Button -->
                            <form action="{{ route('nicknames.destroy', $nickname) }}" method="POST" class="inline"
                                  onsubmit="return confirm('Are you sure you want to delete this nickname location?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="weather-card rounded-lg p-8 text-center">
            <i class="fas fa-map-marker-alt text-4xl text-gray-400 mb-4"></i>
            <h3 class="text-xl font-semibold mb-2">No Nickname Locations</h3>
            <p class="text-gray-300">Add nicknames for your favorite locations to access them quickly.</p>
        </div>
    @endif
</div>

<!-- Edit Nickname Modal -->
<div id="edit-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="weather-card rounded-lg p-6 w-full max-w-md mx-4">
        <h3 class="text-xl font-semibold mb-4">Edit Nickname</h3>
        <form id="edit-form" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Nickname</label>
                <input type="text" id="edit-nickname" name="nickname" 
                       class="w-full px-4 py-2 rounded-lg bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-blue-400" required>
            </div>
            <div class="flex justify-end space-x-4">
                <button type="button" onclick="closeEditModal()" class="px-4 py-2 text-gray-300 hover:text-white">Cancel</button>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Update
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
// Override the selectLocation function for nickname page
function selectLocation(location) {
    document.getElementById('original_name').value = location.name;
    document.getElementById('latitude').value = location.lat;
    document.getElementById('longitude').value = location.lon;
    document.getElementById('selected_location').value = location.name;
    document.getElementById('nickname-form').classList.remove('hidden');
    document.getElementById('search-results').classList.add('hidden');
    document.getElementById('location-search').value = '';
}

function cancelNickname() {
    document.getElementById('nickname-form').classList.add('hidden');
    document.getElementById('location-search').value = '';
}

function viewNicknameWeather(lat, lon, nickname) {
    const url = new URL('{{ route("weather.current") }}');
    url.searchParams.set('lat', lat);
    url.searchParams.set('lon', lon);
    url.searchParams.set('location', nickname);
    window.location.href = url.toString();
}

function editNickname(id, currentNickname) {
    document.getElementById('edit-nickname').value = currentNickname;
    document.getElementById('edit-form').action = `/nicknames/${id}`;
    document.getElementById('edit-modal').classList.remove('hidden');
}

function closeEditModal() {
    document.getElementById('edit-modal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('edit-modal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeEditModal();
    }
});
</script>
@endpush
@endsection