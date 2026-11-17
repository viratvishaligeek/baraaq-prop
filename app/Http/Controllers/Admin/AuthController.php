<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
class AuthController extends Controller
{
    public function create()
    {
        return view('admin.auth.login');
    }
    public function store(Request $r)
    {
        $data = $r->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string']
        ]);
        if (!Auth::attempt($data, $r->boolean('remember')))
            throw ValidationException::withMessages(['email' => 'The supplied credentials are incorrect.']);
        if (!$r->user()->is_active || (!$r->user()->isAdministrator() && !$r->user()->hasPermission('dashboard.view'))) {
            Auth::logout();
            return back()->withErrors(['email' => 'This account is disabled or does not have admin access.']);
        }
        $r->session()->regenerate();
        return redirect()->intended(route('admin.dashboard'));
    }
    public function destroy(Request $r)
    {
        Auth::logout();
        $r->session()->invalidate();
        $r->session()->regenerateToken();
        return redirect()->route('admin.login');
    }
}
