<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->intended('/');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            // Ensure we have an active location set in session for the dashboard and APIs.
            // If the user has one or more locations, pick the first alphabetically (A..Z).
            try {
                $user = Auth::user();
                if ($user) {
                    $firstLoc = $user->locations()->orderBy('name', 'asc')->first();
                    if ($firstLoc) {
                        $request->session()->put('active_location_id', (int) $firstLoc->id);
                    }
                }
            } catch (\Throwable $e) {
                // Be defensive: if anything fails here, don't block login. Log for debugging.
                \Log::warning('Failed setting active_location_id on login: ' . $e->getMessage());
            }

            return redirect()->intended('/');
        }

        return back()->withErrors(['email' => 'Invalid credentials'])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
