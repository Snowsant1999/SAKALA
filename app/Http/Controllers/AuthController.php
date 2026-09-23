<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * Mock user accounts for demo purposes.
     */
    private array $mockUsers = [
        'student@sakala.test' => [
            'id' => 'usr-001',
            'name' => 'Andi Pratama',
            'email' => 'student@sakala.test',
            'role' => 'student',
            'nim' => '2341720001',
            'departmentId' => 'dept-01',
            'studyProgramId' => 'sp-01',
        ],
        'lecturer@sakala.test' => [
            'id' => 'usr-004',
            'name' => 'Dr. Budi Santoso',
            'email' => 'lecturer@sakala.test',
            'role' => 'lecturer',
            'nidn' => '0012345678',
            'departmentId' => 'dept-01',
            'studyProgramId' => 'sp-01',
        ],
        'admin@sakala.test' => [
            'id' => 'usr-006',
            'name' => 'Admin SAKALA',
            'email' => 'admin@sakala.test',
            'role' => 'admin',
        ],
    ];

    /**
     * Show the login form.
     */
    public function login()
    {
        if (session('user_role')) {
            return $this->redirectByRole(session('user_role'));
        }

        return view('auth.login');
    }

    /**
     * Authenticate using mock data (session-based).
     */
    public function authenticate(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $email = $request->input('email');

        if (!isset($this->mockUsers[$email])) {
            return back()->with('error', 'Email tidak ditemukan. Gunakan salah satu demo account.')->withInput();
        }

        $user = $this->mockUsers[$email];

        // Store user data in session
        session([
            'user_id' => $user['id'],
            'user_name' => $user['name'],
            'user_email' => $user['email'],
            'user_role' => $user['role'],
            'user_data' => $user,
        ]);

        return $this->redirectByRole($user['role']);
    }

    /**
     * Logout — clear session and redirect to login.
     */
    public function logout()
    {
        session()->flush();

        return redirect('/login')->with('success', 'Berhasil logout.');
    }

    /**
     * Redirect user based on their role.
     */
    private function redirectByRole(string $role)
    {
        return match ($role) {
            'admin' => redirect('/admin/dashboard'),
            default => redirect('/dashboard'),
        };
    }
}
