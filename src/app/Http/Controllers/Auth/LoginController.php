<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    /**
     * After the user is authenticated, generate a JWT and store it in session
     * so the /home view can push it into localStorage.
     */
    protected function authenticated(Request $request, $user)
    {
        $token = $this->generateJwt($user);
        \Log::info('Generated JWT: ' . $token);

        // Put it in session so we can read it in the home blade and store in localStorage
        session(['jwt' => $token]);

        // If you login via AJAX, you can return it directly:
        if ($request->wantsJson()) {
            return response()->json(['token' => $token]);
        }

        return redirect()->intended($this->redirectTo);
    }

    /**
     * Generate JWT for the authenticated user.
     */
    protected function generateJwt($user): string
    {
        $now   = time();
        $ttl   = config('jwt.ttl');
        $payload = [
            'iss' => config('jwt.issuer'),
            'aud' => config('jwt.audience'),
            'iat' => $now,
            'nbf' => $now,
            'exp' => $now + $ttl,
            // What you put here is up to you – keep it minimal
            'sub' => $user->id,
            'email' => $user->email,
            'role'  => $user->role,          // or $user->role->name if you use FK
            'login_id' => $user->login_id ?? null,
        ];

        return JWT::encode($payload, config('jwt.secret'), config('jwt.algo'));
    }
}
