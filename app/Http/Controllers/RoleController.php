<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware; // Mengimpor interface HasMiddleware untuk menambahkan middleware ke controller
use Illuminate\Routing\Controllers\Middleware; // Mengimpor kelas Middleware untuk menangani otentikasi akses per aksi
use Spatie\Permission\Models\Role; // Mengimpor model Role dari Spatie Permission untuk mengelola peran (roles)
use Spatie\Permission\Models\Permission; // Mengimpor model Permission dari Spatie Permission untuk mengelola izin (permissions)

class RoleController extends Controller implements HasMiddleware // Mengimplementasikan middleware Spatie
{
    /**
     * Menentukan middleware yang akan diterapkan pada controller ini.
     */
    public static function middleware()
    {
        return [
            // Middleware untuk izin 'roles index' diterapkan hanya pada aksi 'index'
            new Middleware('permission:roles index', only: ['index']),
            // Middleware untuk izin 'roles create' diterapkan hanya pada aksi 'create' dan 'store'
            new Middleware('permission:roles create', only: ['create', 'store']),
            // Middleware untuk izin 'roles edit' diterapkan hanya pada aksi 'edit' dan 'update'
            new Middleware('permission:roles edit', only: ['edit', 'update']),
            // Middleware untuk izin 'roles delete' diterapkan hanya pada aksi 'destroy'
            new Middleware('permission:roles delete', only: ['destroy']),
        ];
    }

    /**
     * Menampilkan daftar role yang ada.
     */
    public function index(Request $request)
    {
        // Mengambil daftar role dengan mengikutsertakan permissions terkait
        $roles = Role::select('id', 'name')
            ->with('permissions:id,name') // Menyertakan data permissions yang terhubung dengan role
            ->when($request->search, fn($search) => $search->where('name', 'like', '%'.$request->search.'%')) // Pencarian berdasarkan nama role
            ->latest() // Mengurutkan berdasarkan role yang terbaru
            ->paginate(6); // Paginate hasilnya dengan 6 item per halaman

        // Menampilkan tampilan 'Roles/Index' dengan data roles dan filter pencarian
        return inertia('Roles/Index', ['roles' => $roles, 'filters' => $request->only(['search'])]);
    }

    /**
     * Menampilkan form untuk membuat role baru.
     */
    public function create()
    {
        // Mengambil semua permissions dan mengelompokkan berdasarkan kata pertama dari nama permission
        $data = Permission::orderBy('name')->pluck('name', 'id');
        $collection = collect($data);
        $permissions = $collection->groupBy(function ($item, $key) {
            // Memecah nama permission menjadi array kata-kata dan mengambil kata pertama
            $words = explode(' ', $item);
            return $words[0];
        });

        // Menampilkan tampilan untuk membuat role dengan data permissions
        return inertia('Roles/Create', ['permissions' => $permissions]);
    }

    /**
     * Menyimpan role baru ke dalam database.
     */
    public function store(Request $request)
    {
        // Validasi data request untuk memastikan nama role unik dan permissions dipilih
        $request->validate([
            'name' => 'required|min:3|max:255|unique:roles', // Nama role harus unik
            'selectedPermissions' => 'required|array|min:1', // Permissions yang dipilih harus ada
        ]);

        // Membuat role baru berdasarkan nama yang diberikan
        $role = Role::create(['name' => $request->name]);

        // Memberikan permissions yang dipilih kepada role
        $role->givePermissionTo($request->selectedPermissions);

        // Mengarahkan kembali ke halaman daftar role
        return to_route('roles.index');
    }

    /**
     * Menampilkan form untuk mengedit role yang sudah ada.
     */
    public function edit(Role $role)
    {
        // Mengambil dan mengelompokkan permissions yang ada untuk ditampilkan
        $data = Permission::orderBy('name')->pluck('name', 'id');
        $collection = collect($data);
        $permissions = $collection->groupBy(function ($item, $key) {
            $words = explode(' ', $item);
            return $words[0];
        });

        // Memuat permissions yang sudah ada pada role ini
        $role->load('permissions');

        // Menampilkan tampilan untuk mengedit role dengan data role dan permissions
        return inertia('Roles/Edit', ['role' => $role, 'permissions' => $permissions]);
    }

    /**
     * Memperbarui role yang ada di dalam database.
     */
    public function update(Request $request, Role $role)
    {
        // Validasi data request untuk memastikan nama role unik dan permissions dipilih
        $request->validate([
            'name' => 'required|min:3|max:255|unique:roles,name,'.$role->id, // Nama role harus unik kecuali untuk role yang sedang diedit
            'selectedPermissions' => 'required|array|min:1', // Permissions yang dipilih harus ada
        ]);

        // Memperbarui data nama role
        $role->update(['name' => $request->name]);

        // Menyinkronkan permissions baru yang dipilih dengan role
        $role->syncPermissions($request->selectedPermissions);

        // Mengarahkan kembali ke halaman daftar role setelah update
        return to_route('roles.index');
    }

    /**
     * Menghapus role dari database.
     */
    public function destroy(Role $role)
    {
        // Menghapus role yang dipilih dari database
        $role->delete();

        // Mengarahkan kembali ke halaman sebelumnya setelah penghapusan
        return back();
    }
}
