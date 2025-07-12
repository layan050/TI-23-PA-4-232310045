<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Weatherly</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            min-height: 100vh;
            font-family: 'Inter', sans-serif;
        }
        .auth-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
    </style>
</head>

{{-- logo --}}
<body class="text-white flex items-center justify-center min-h-screen">
    <div class="w-full max-w-md mx-4">
        <!-- Logo -->
        <div class="text-center mb-8">
            <div class="flex items-center justify-center space-x-2 mb-4">
                <i class="fas fa-cloud text-4xl"></i>
                <span class="text-3xl font-bold">Weatherly</span>
            </div>
            <p class="text-gray-300">Sign in to your account</p>
        </div>

        <!-- Login Form -->
        <div class="auth-card rounded-2xl p-8">
            <form method="POST" action="{{ route('login') }}">
                @csrf
                
                <!-- Email -->
                <div class="mb-6">
                    <label for="email" class="block text-sm font-medium mb-2">Email Address</label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           value="{{ old('email') }}"
                           class="w-full px-4 py-3 rounded-lg bg-white bg-opacity-20 text-white placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:bg-opacity-30 transition-all"
                           placeholder="Enter your email"
                           required>
                    @error('email')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div class="mb-6">
                    <label for="password" class="block text-sm font-medium mb-2">Password</label>
                    <input type="password" 
                           id="password" 
                           name="password"
                           class="w-full px-4 py-3 rounded-lg bg-white bg-opacity-20 text-white placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:bg-opacity-30 transition-all"
                           placeholder="Enter your password"
                           required>
                    @error('password')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between mb-6">
                    <label class="flex items-center">
                        <input type="checkbox" name="remember" class="rounded bg-white bg-opacity-20 border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm">Remember me</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <i class="fas fa-sign-in-alt mr-2"></i>
                    Sign In
                </button>
            </form>

            <!-- Register Link -->
            <div class="text-center mt-6">
                <p class="text-gray-300">
                    Don't have an account? 
                    <a href="{{ route('register') }}" class="text-blue-400 hover:text-blue-300 font-medium">Sign up</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>