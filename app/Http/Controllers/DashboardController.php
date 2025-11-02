<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __construct(
        private AuthService $authService,
    ){}

    public function index(Request $request, string $token) 
    {
        // Check if URL has expired
        if (!$request->hasValidSignature()) {
            // If expired redirect to welcome page
            return redirect()->route('welcome')->with(
                'info', 'Invalid URL Signature. Please send a request again'
            );
        }

        // Check user detail cache status
        $status = $this->authService->checkUserCache($token);

        // Check if user cache false
        if (!$status) {
            // If false redirect to welcome page
            return redirect()->route('welcome')->with(
                'info', 'Dashboard url has expired. Please send a request again'
            );
        }

        // Fetch user cache data
        $cacheData = $this->authService->getUserCache($token);

        // Store user cache data
        $data = $this->authService->storeUserCacheData($cacheData);

        // Forget user cache
        // $this->authService->forgetUserCache($token); -> uncomment on sign out refresh

        return Inertia::render('dashboard', [
            'data' => $data
        ]);
    }    
}
