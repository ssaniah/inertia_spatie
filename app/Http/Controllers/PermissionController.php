<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware; // Mengimpor interface HasMiddleware
use Illuminate\Routing\Controllers\Middleware; // Mengimpor kelas Middleware untuk menangani middleware per aksi
use Spatie\Permission\Models\Permission; // Mengimpor model Permission dari package Spatie untuk mengelola permissions

class PermissionController extends Controller implements HasMiddleware
{
    /**
     * Menentukan middleware yang akan digunakan oleh controller ini
     */
    public static function middleware()
    {
        // Mendefinisikan middleware untuk setiap metode di controller ini
        return [
            // Middleware untuk izin 'permissions index' hanya diterapkan pada metode 'index'
            new Middleware('permission:permissions index', only: ['index']),
            // Middleware untuk izin 'permissions create' hanya diterapkan pada metode 'create' dan 'store'
            new Middleware('permission:permissions create', only: ['create', 'store']),
            // Middleware untuk izin 'permissions edit' hanya diterapkan pada metode 'edit' dan 'update'
            new Middleware('permission:permissions edit', only: ['edit', 'update']),
            // Middleware untuk izin 'permissions delete' hanya diterapkan pada metode 'destroy'
            new Middleware('permission:permissions delete', only: ['destroy']),
        ];
    }

    /**
     * Menampilkan daftar semua permissions.
     */
    public function index(Request $request)
    {
        // Mengambil daftar permission dengan fitur pencarian dan pagination
        $permissions = Permission::select('id', 'name')
            ->when($request->search, fn($search) => $search->where('name', 'like', '%'.$request->search.'%')) // Filter pencarian berdasarkan nama
            ->latest() // Mengurutkan berdasarkan waktu pembuatan terbaru
            ->paginate(6)->withQueryString(); // Menggunakan pagination dengan 6 item per halaman

        // Menampilkan tampilan dengan data permissions dan filter pencarian
        return inertia('Permissions/Index', ['permissions' => $permissions, 'filters' => $request->only(['search'])]);
    }

    /**
     * Menampilkan formulir untuk membuat permission baru.
     */
    public function create()
    {
        // Menampilkan tampilan untuk form create permission
        return inertia('Permissions/Create');
    }

    /**
     * Menyimpan permission yang baru dibuat ke dalam storage (database).
     */
    public function store(Request $request)
    {
        // Validasi data request untuk memastikan nama permission valid
        $request->validate(['name' => 'required|min:3|max:255|unique:permissions']); // Nama permission harus unik

        // Membuat permission baru dengan nama yang diberikan
        Permission::create(['name' => $request->name]);

        // Mengarahkan kembali ke daftar permissions setelah sukses
        return to_route('permissions.index');
    }

    /**
     * Menampilkan formulir untuk mengedit permission yang sudah ada.
     */
    public function edit(Permission $permission)
    {
        // Menampilkan tampilan untuk form edit permission
        return inertia('Permissions/Edit', ['permission' => $permission]);
    }

    /**
     * Memperbarui data permission yang telah ada di storage.
     */
    public function update(Request $request, Permission $permission)
    {
        // Validasi data request untuk memperbarui nama permission
        $request->validate(['name' => 'required|min:3|max:255|unique:permissions,name,'.$permission->id]); // Nama permission harus unik kecuali untuk permission yang sedang diedit

        // Memperbarui nama permission yang sesuai dengan data yang diterima
        $permission->update(['name' => $request->name]);

        // Mengarahkan kembali ke daftar permissions setelah sukses
        return to_route('permissions.index');
    }

    /**
     * Menghapus permission yang dipilih dari storage.
     */
    public function destroy(Permission $permission)
    {
        // Menghapus permission dari database
        $permission->delete();

        // Mengarahkan kembali ke halaman sebelumnya setelah sukses menghapus
        return back();
    }
}
