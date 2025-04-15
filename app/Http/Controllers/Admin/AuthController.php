<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; 

class AuthController extends Controller
{
    // Menampilkan halaman form login
    public function showLoginForm()
    {
        Log::info('Menampilkan halaman form login admin.'); // Logging saat form login ditampilkan
        return view('admin.pages.auth.login'); // Mengembalikan tampilan form login
    }

    // Proses login pengguna
    public function login(Request $request)
    {
        // Validasi input email dan password
        $credentials = $request->validate([
            'email' => 'required|email',   // Email wajib diisi dan harus format email yang valid
            'password' => 'required|min:6', // Password wajib diisi dan minimal 6 karakter
        ]);

        Log::info('Percobaan login dimulai.', [
            'email' => $credentials['email'], // Logging email yang mencoba login
        ]);

        // Coba melakukan autentikasi dengan kredensial yang diberikan
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate(); // Regenerasi session untuk keamanan setelah login

            $user = Auth::user(); // Ambil data user yang sedang login

            // Logging jika login berhasil
            Log::info('Login berhasil.', [
                'user_id' => $user->id,
                'email' => $user->email,
                'role' => $user->role
            ]);
            
            // Cek role user setelah login untuk menentukan halaman tujuan
            if (Auth::user()->role == 'kasir') {
                return redirect()->route('kasir.shift')->with('success', 'Login berhasil sebagai Kasir!'); // Redirect ke halaman penjualan jika user adalah kasir
            } elseif (Auth::user()->role == 'chef') {
                return redirect()->route('chef.dashboard')->with('success', 'Login berhasil sebagai Chef!'); // Redirect ke halaman chef jika user adalah chef
            } elseif (Auth::user()->role == 'owner') {
                return redirect()->route('dashboard')->with('success', 'Login berhasil sebagai Owner!'); // Redirect ke halaman dashboard jika user adalah admin
            }
            return redirect()->route('dashboard')->with('success', 'Login berhasil sebagai Admin!'); // Redirect ke dashboard jika bukan kasir atau chef
        }

         // Logging jika login gagal
         Log::warning('Login gagal.', [
            'email' => $credentials['email'],
            'ip' => $request->ip()
        ]);

        
        // Jika login gagal, kembalikan ke halaman login dengan pesan error
        return back()->withErrors([
            'email' => 'Email atau password salah!', // Menampilkan pesan kesalahan jika login gagal
        ])->onlyInput('email'); // Hanya input email yang tetap diisi, password dikosongkan
    }

    // Logout pengguna
    public function logout(Request $request)
    {
         // Logging sebelum logout
        Log::info('User melakukan logout.', [
            'user_id' => Auth::id(),
            'email' => Auth::user()->email ?? null
        ]);
        Auth::logout(); // Logout user dari session
        $request->session()->invalidate(); // Menghapus session yang aktif
        $request->session()->regenerateToken(); // Regenerasi token CSRF untuk keamanan
        return redirect()->route('admin.login')->with('logout', 'Anda telah logout!'); // Redirect ke halaman login setelah logout
    }
}
