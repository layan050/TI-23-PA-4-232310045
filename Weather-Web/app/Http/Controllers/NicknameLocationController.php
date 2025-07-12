<?php

namespace App\Http\Controllers;

use App\Models\NicknameLocation;
use App\Services\WeatherService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NicknameLocationController extends Controller
{
    private $weatherService;

    public function __construct(WeatherService $weatherService)
    {
        $this->weatherService = $weatherService;
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        $nicknames = $user->nicknameLocations()->orderBy('created_at', 'desc')->get();

        return view('nicknames.index', compact('nicknames'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'original_name' => 'required|string|max:255',
            'nickname' => 'required|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $user = Auth::user();

        // Check if nickname already exists for this user
        $existingNickname = $user->nicknameLocations()
            ->where('nickname', $request->nickname)
            ->first();

        if ($existingNickname) {
            return back()->withErrors(['nickname' => 'This nickname already exists.']);
        }

        NicknameLocation::create([
            'user_id' => $user->id,
            'original_name' => $request->original_name,
            'nickname' => $request->nickname,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        return back()->with('success', 'Nickname location saved successfully!');
    }

    public function update(Request $request, NicknameLocation $nickname)
    {
        // Check if user owns this nickname location
        if ($nickname->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'nickname' => 'required|string|max:255',
        ]);

        // Check if new nickname already exists for this user (excluding current one)
        $existingNickname = Auth::user()->nicknameLocations()
            ->where('nickname', $request->nickname)
            ->where('id', '!=', $nickname->id)
            ->first();

        if ($existingNickname) {
            return back()->withErrors(['nickname' => 'This nickname already exists.']);
        }

        $nickname->update([
            'nickname' => $request->nickname,
        ]);

        return back()->with('success', 'Nickname updated successfully!');
    }

    public function destroy(NicknameLocation $nickname)
    {
        // Check if user owns this nickname location
        if ($nickname->user_id !== Auth::id()) {
            abort(403);
        }

        $nickname->delete();

        return back()->with('success', 'Nickname location deleted successfully!');
    }

    public function getWeather(NicknameLocation $nickname)
    {
        // Check if user owns this nickname location
        if ($nickname->user_id !== Auth::id()) {
            abort(403);
        }

        $weather = $this->weatherService->getCurrentWeather(
            $nickname->latitude,
            $nickname->longitude
        );

        return response()->json([
            'nickname' => $nickname,
            'weather' => $weather
        ]);
    }
}