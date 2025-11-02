<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class AuthController extends Controller
{
    public function __construct(
        private AuthService $authService,
    ){}

    public function validate(Request $request) 
    {
        try {
            // Validate email
            $validated = $request->validate([
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users']
            ]);

            // Get validated email
            $email = $validated['email'];

            // Create a random 32-length password
            $password = $this->authService->createPassword();

            // Create a random 64-length token
            $token = $this->authService->createToken();

            // Create a cache with specific token
            $this->authService->createCache($token, $email, $password);
            // Fetch cache data
            // $cacheData = $this->authService->getCache($token);

            // Create a verification url
            $verificationUrl = $this->authService->createVerificationUrl($token);

            Mail::raw(
                "Disposable Password: {$password}",
                function ($message) use ($email) {
                    $message->to($email)
                        ->subject('Your disposable password');
                }
            );

            // Redirect to new created verification url
            return redirect($verificationUrl);
        } catch (Exception $e) {
            Log::error('Failed to create password', [
                'email' => "email: {$request->input('email')}",
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()->with([
                'error' => "An error occured while sending password to your email"
            ]);
        }
    }

    public function verification(Request $request, string $token)
    {
        // Check if the verification link is expired
        if (!$request->hasValidSignature()) {
            // If expired redirect to welcome page
            return redirect()->route('welcome')->with(
                'info', 'Invalid URL Signature. Please send a request again.'
            );
        }

        return Inertia::render('verify', [
            'token' => $token,
        ]);
    }

    public function verify(Request $request, string $token) 
    {
        try {
            // Validate password
            $validated = $request->validate([
                'password' => ['required', 'string', 'max:255'],
            ]);

            // Store validated password
            $password = $this->authService->preparedPassword($validated['password']);

            // Check cache existence first
            $cacheStatus = $this->authService->checkCache($token);

            // Check if cache status false
            if (!$cacheStatus) {
                // If false redirect to welcome page
                return redirect()->route('welcome')->with(
                    'info', 'Verification link has expired. Please send a request again'
                );
            }

            // Fetch cache data
            $cacheData = $this->authService->getCache($token);

            // Store cache data in an array
            // $data = $this->authService->storeCacheData($cacheData); -> potential sequence (idk to be think about)

            // Verify Password
            $status = $this->authService->verifyPassword($password, $cacheData);

            // Check verification status
            if (!$status) {
                // If false redirect to welcome page
                return redirect()->route('welcome')->with(
                    'error', 'Invalid Password. Please send a request again'
                );
            }

            // Create a new user detail cache
            $this->authService->createUserDetailCache($token, $cacheData['email']);

            // Create new user dashboard url with token
            $dashboardUrl = $this->authService->createUserDashboardUrl($token);

            // Forget cache
            $this->authService->forgetCache($token);

            return redirect($dashboardUrl);
        } catch (Exception $e) {
            Log::error('Failed to verify password in controller', [
                'error' => $e->getMessage(),
                'token' => $token
            ]);

            return redirect()->route('welcome')->with(
                'error', "There's an error in verifying your password. Please try again"
            );
        }
    }
}
