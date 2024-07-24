<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Construct Middleware Access
     */
    public function __construct()
    {
        /**
         * As Guest For All Function Controller Except Logout Function (Auth Middleware)
         */
        $this->middleware('guest')->except('logout');
    }

    /**
     * View Login Function
     */
    public function login()
    {
        return view('auth.login');
    }

    /**
     * Authenticate Login Function
     */
    public function authenticate(Request $request)
    {
        try {
            /**
             * Filtering Email or Username Value
             */
            $email_or_username = $request->input('username');
            $field = filter_var($email_or_username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
            $request->merge([$field => $email_or_username]);

            /**
             * Request Validation
             */
            $request->validate([
                $field => 'required',
                'password' => 'required',
            ]);

            /**
             * User Validation Fom Request Email or Username
             */
            $user = User::where($field, $email_or_username)->first();

            /**
             * Validation User Check
             */
            if (!is_null($user)) {

                /**
                 * Login Authenticate
                 */
                if (Auth::attempt([$field => $email_or_username, 'password' => $request->password], isset($request->remember))) {
                    $request->session()->regenerate();
                    $intended_url = session()->pull('url.intended', route('home'));
                    return redirect()->to($intended_url);
                } else {
                    return redirect()
                        ->back()
                        ->withErrors(['username' => 'These credentials do not match our records.'])
                        ->withInput();
                }
            } else {
                return redirect()
                    ->back()
                    ->withErrors(['username' => 'These credentials do not match our records.'])
                    ->withInput();
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Logout Function
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
