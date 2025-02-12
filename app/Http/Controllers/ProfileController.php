<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest; // Mengimpor request untuk validasi pembaruan profil
use Illuminate\Contracts\Auth\MustVerifyEmail; // Mengimpor interface untuk memverifikasi email
use Illuminate\Http\RedirectResponse; // Mengimpor response untuk pengalihan setelah update
use Illuminate\Http\Request; // Mengimpor request untuk menangani data yang dikirim oleh pengguna
use Illuminate\Support\Facades\Auth; // Mengimpor facade Auth untuk mengelola autentikasi
use Illuminate\Support\Facades\Redirect; // Mengimpor facade Redirect untuk pengalihan setelah aksi
use Inertia\Inertia; // Mengimpor Inertia untuk merender tampilan berbasis React/Vue
use Inertia\Response; // Mengimpor Response untuk tipe response dari Inertia

class ProfileController extends Controller
{
    /**
     * Menampilkan formulir profil pengguna.
     */
    public function edit(Request $request): Response
    {
        // Merender tampilan Profile/Edit dengan data terkait
        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail, // Mengecek apakah pengguna perlu memverifikasi email
            'status' => session('status'), // Menyertakan status jika ada (misalnya pesan sukses)
        ]);
    }

    /**
     * Memperbarui informasi profil pengguna.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        // Mengisi data pengguna yang diperbarui dengan data yang divalidasi dari request
        $request->user()->fill($request->validated());

        // Memeriksa apakah email telah berubah dan mengatur email_verified_at menjadi null jika berubah
        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        // Menyimpan perubahan pada pengguna
        $request->user()->save();

        // Mengalihkan pengguna kembali ke halaman edit profil
        return Redirect::route('profile.edit');
    }

    /**
     * Menghapus akun pengguna.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Validasi password saat menghapus akun, pastikan itu adalah password saat ini
        $request->validate([
            'password' => ['required', 'current_password'], // Memeriksa apakah password yang diberikan benar
        ]);

        $user = $request->user(); // Mendapatkan instance pengguna

        Auth::logout(); // Keluar dari sesi pengguna yang sedang aktif

        $user->delete(); // Menghapus data pengguna dari database

        $request->session()->invalidate(); // Menghapus sesi pengguna
        $request->session()->regenerateToken(); // Menghasilkan token CSRF baru untuk keamanan

        // Mengalihkan pengguna ke halaman utama setelah akun dihapus
        return Redirect::to('/');
    }
}
