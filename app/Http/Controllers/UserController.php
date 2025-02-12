<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class UserController extends Controller implements HasMiddleware
{
    /**
     * Menentukan middleware yang akan diterapkan pada aksi-aksi tertentu.
     */
    public static function middleware()
    {
        return [
            // Middleware untuk izin 'users index' diterapkan hanya pada aksi 'index'
            new Middleware('permission:users index', only: ['index']),
            // Middleware untuk izin 'users create' diterapkan hanya pada aksi 'create' dan 'store'
            new Middleware('permission:users create', only: ['create', 'store']),
            // Middleware untuk izin 'users edit' diterapkan hanya pada aksi 'edit' dan 'update'
            new Middleware('permission:users edit', only: ['edit', 'update']),
            // Middleware untuk izin 'users delete' diterapkan hanya pada aksi 'destroy'
            new Middleware('permission:users delete', only: ['destroy']),
        ];
    }

    /**
     * Menampilkan daftar semua pengguna (users).
     */
    public function index(Request $request)
    {
        // Mengambil semua pengguna beserta peran mereka, dan jika ada pencarian, filter berdasarkan 'name'
        $users = User::with('roles')
            ->when(request('search'), fn($query) => $query->where('name', 'like', '%'.request('search').'%'))
            ->latest() // Urutkan berdasarkan yang terbaru
            ->paginate(6); // Membatasi hasil dengan 6 pengguna per halaman

        // Menampilkan tampilan 'Users/Index' dengan data pengguna dan filter pencarian
        return inertia('Users/Index', ['users' => $users, 'filters' => $request->only(['search'])]);
    }

    /**
     * Menampilkan formulir untuk membuat pengguna baru.
     */
    public function create()
    {
        // Mengambil semua peran yang tersedia untuk diberikan ke pengguna baru
        $roles = Role::latest()->get();
        
        // Menampilkan tampilan 'Users/Create' dengan data peran yang tersedia
        return inertia('Users/Create', ['roles' => $roles]);
    }

    /**
     * Menyimpan pengguna baru ke dalam penyimpanan.
     */
    public function store(Request $request)
    {
        // Memvalidasi data yang diterima
        $request->validate([
            'name' => 'required|min:3|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|min:4', // Konfirmasi password
            'selectedRoles' => 'required|array|min:1', // Setidaknya satu peran harus dipilih
        ]);

        // Membuat data pengguna baru dengan data yang sudah divalidasi
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password), // Enkripsi password
        ]);

        // Memberikan peran yang dipilih kepada pengguna yang baru dibuat
        $user->assignRole($request->selectedRoles);

        // Mengarahkan ke halaman daftar pengguna
        return to_route('users.index');
    }

    /**
     * Menampilkan detail pengguna tertentu (lihat detail pengguna).
     */
    public function show(string $id)
    {
        // Metode ini belum diimplementasikan.
        //
        // Anda bisa menambahkan fungsionalitas di sini jika ingin menampilkan detail untuk pengguna tertentu.
    }

    /**
     * Menampilkan formulir untuk mengedit pengguna tertentu.
     */
    public function edit(User $user)
    {
        // Mengambil peran yang tersedia kecuali 'super-admin'
        $roles = Role::where('name', '!=', 'super-admin')->get();

        // Memuat peran yang dimiliki pengguna
        $user->load('roles');

        // Menampilkan tampilan 'Users/Edit' dengan data pengguna dan peran yang tersedia
        return inertia('Users/Edit', ['user' => $user, 'roles' => $roles]);
    }

    /**
     * Memperbarui data pengguna yang telah ada di penyimpanan.
     */
    public function update(Request $request, User $user)
    {
        // Memvalidasi data yang diterima
        $request->validate([
            'name' => 'required|min:3|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id, // Mengecualikan email pengguna yang sedang diedit dari pemeriksaan keunikan
            'selectedRoles' => 'required|array|min:1',
        ]);

        // Memperbarui data pengguna
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        // Menyinkronkan peran yang dipilih dengan pengguna
        $user->syncRoles($request->selectedRoles);

        // Mengarahkan ke halaman daftar pengguna
        return to_route('users.index');
    }

    /**
     * Menghapus pengguna dari penyimpanan.
     */
    public function destroy(User $user)
    {
        // Menghapus data pengguna
        $user->delete();

        // Mengarahkan kembali ke halaman sebelumnya
        return back();
    }
}
