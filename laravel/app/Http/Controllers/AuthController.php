<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function loginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($credentials)) {
            return back()->withErrors(['email' => 'Неверный email или пароль.']);
        }

        $user = Auth::user();

        if (!$user->is_active) {
            Auth::logout();
            return back()->withErrors(['email' => 'Ваш аккаунт заблокирован.']);
        }

        $request->session()->regenerate();
        return redirect()->intended('/');
    }

    public function registerForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
            'role' => 'required|in:reader,publisher',  
        ]);

        $role = Role::where('name', $data['role'])->first();

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role_id' => $role->id,  
        ]);

        Auth::login($user);
        return redirect('/');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    public function githubRedirect()
    {
        return Socialite::driver('github')->redirect();
    }

    public function githubCallback()
    {
        $githubUser = Socialite::driver('github')->user();

        $user = User::where('github_id', $githubUser->getId())->first();

        if (!$user) {
            $user = User::where('email', $githubUser->getEmail())->first();
        }

        $isNewUser = false;

        if (!$user) {
            $role = Role::where('name', 'reader')->first();
            $user = User::create([
                'name' => $githubUser->getName() ?? $githubUser->getNickname(),
                'email' => $githubUser->getEmail(),
                'github_id' => $githubUser->getId(),
                'role_id' => $role->id,
            ]);
            $isNewUser = true;
        } else {
            $user->update(['github_id' => $githubUser->getId()]);
        }

        if (!$user->is_active) {
            return redirect('/login')->withErrors(['email' => 'Ваш аккаунт заблокирован.']);
        }

        Auth::login($user);
        
        if ($isNewUser) {
            return redirect()->route('role.choose');
        }
        
        return redirect('/');
    }

    public function chooseRoleForm()
    {
        $user = Auth::user();
        if ($user->role->name !== 'reader') {
            return redirect('/');
        }
        
        if ($user->posts()->count() > 0 || $user->comments()->count() > 0) {
            return redirect('/');
        }
        
        return view('auth.choose-role');
    }

    public function chooseRole(Request $request)
    {
        $data = $request->validate([
            'role' => 'required|in:reader,publisher',
        ]);
        
        $user = Auth::user();
        
        if ($user->role->name !== 'reader') {
            return redirect('/')->withErrors(['role' => 'Роль уже выбрана.']);
        }
        
        $role = Role::where('name', $data['role'])->first();
        $user->update(['role_id' => $role->id]);
        
        return redirect('/')->with('success', 'Роль успешно выбрана!');
    }
}