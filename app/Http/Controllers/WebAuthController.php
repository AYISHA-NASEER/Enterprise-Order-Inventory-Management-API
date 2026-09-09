<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use App\Enums\UserRole;

class WebAuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Show Login Page
    |--------------------------------------------------------------------------
    */

    public function showLogin(): View
    {
        return view('auth.login');
    }


    /*
    |--------------------------------------------------------------------------
    | Login
    |--------------------------------------------------------------------------
    */

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);


        /*
        |--------------------------------------------------------------------------
        | Check Credentials
        |--------------------------------------------------------------------------
        */

        if (!Auth::attempt($credentials)) {

            return back()
                ->withErrors([
                    'email' => 'Invalid email or password.',
                ])
                ->withInput(
                    $request->only('email')
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Regenerate Session
        |--------------------------------------------------------------------------
        */

        $request->session()->regenerate();


        /*
        |--------------------------------------------------------------------------
        | Get Logged-in User
        |--------------------------------------------------------------------------
        */

        $user = Auth::user();


        /*
        |--------------------------------------------------------------------------
        | Redirect Based on Role
        |--------------------------------------------------------------------------
        */

        return match ($user->role) {

            UserRole::Customer => redirect()
                ->route('customer.dashboard'),

            UserRole::Admin => redirect()
                ->route('dashboard'),

            UserRole::Manager => abort(
                403,
                'Manager dashboard is not available yet.'
            ),

            UserRole::Warehouse => redirect()->route('warehouse.dashboard'),


            default => abort(
                403,
                'Invalid user role.'
            ),
        };
    }


    /*
    |--------------------------------------------------------------------------
    | Show Register Page
    |--------------------------------------------------------------------------
    */

    public function showRegister(): View
    {
        return view('auth.register');
    }


    /*
    |--------------------------------------------------------------------------
    | Register
    |--------------------------------------------------------------------------
    */

    public function register(Request $request): RedirectResponse
    {
        /*
        |--------------------------------------------------------------------------
        | Validate Registration
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
            ],

            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | Create Customer
        |--------------------------------------------------------------------------
        |
        | Public registration always creates a customer.
        |
        */

        $user = User::create([

            'name' => $validated['name'],

            'email' => $validated['email'],

            'password' => Hash::make(
                $validated['password']
            ),

            'role' => 'customer',

        ]);


        /*
        |--------------------------------------------------------------------------
        | Login User Automatically
        |--------------------------------------------------------------------------
        */

        Auth::login($user);


        /*
        |--------------------------------------------------------------------------
        | Regenerate Session
        |--------------------------------------------------------------------------
        */

        $request->session()->regenerate();


        /*
        |--------------------------------------------------------------------------
        | Customer Dashboard
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route('customer.dashboard');
    }


    /*
    |--------------------------------------------------------------------------
    | Logout
    |--------------------------------------------------------------------------
    */

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();


        /*
        |--------------------------------------------------------------------------
        | Destroy Session
        |--------------------------------------------------------------------------
        */

        $request->session()->invalidate();

        $request->session()->regenerateToken();


        /*
        |--------------------------------------------------------------------------
        | Back to Login
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route('login');
    }
}