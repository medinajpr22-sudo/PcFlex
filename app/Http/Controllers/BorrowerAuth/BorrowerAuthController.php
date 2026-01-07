<?php

namespace App\Http\Controllers\BorrowerAuth;

use App\Http\Controllers\Controller;
use App\Models\Borrower_users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class BorrowerAuthController extends Controller
{
    /**
     * Mostrar formulario de login
     */
    public function showLogin()
    {
        return Inertia::render('Borrower/Login');
    }

    /**
     * Procesar login
     */
    public function login(Request $request)
    {
        $request->validate([
            'number_identification' => 'required',
            'password' => 'required',
        ]);

        $credentials = [
            'number_identification' => $request->number_identification,
            'password' => $request->password,
        ];

        if (Auth::guard('borrower')->attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            
            return redirect()->intended(route('borrower.dashboard'));
        }

        return back()->withErrors([
            'number_identification' => 'Las credenciales no coinciden con nuestros registros.',
        ])->onlyInput('number_identification');
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        Auth::guard('borrower')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('borrower.login');
    }

    /**
     * Mostrar formulario de registro (opcional - si permites auto-registro)
     */
    public function showRegister()
    {
        return Inertia::render('Borrower/Register');
    }

    /**
     * Procesar registro (opcional)
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:60',
            'last_name' => 'required|string|max:60',
            'type_identification' => 'required|string',
            'number_identification' => 'required|string|unique:borrower_users,number_identification',
            'sex_user' => 'required|string',
            'gender_sex' => 'required|string',
            'roll' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = Borrower_users::create([
            'name' => $request->name,
            'last_name' => $request->last_name,
            'type_identification' => $request->type_identification,
            'number_identification' => $request->number_identification,
            'sex_user' => $request->sex_user,
            'gender_sex' => $request->gender_sex,
            'roll' => $request->roll,
            'password' => Hash::make($request->password),
            'status' => 'activo',
        ]);

        Auth::guard('borrower')->login($user);

        return redirect()->route('borrower.dashboard');
    }
}
