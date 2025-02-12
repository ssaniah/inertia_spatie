<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission; // Mengimpor model Permission dari package Spatie untuk mengelola permissions
use Illuminate\Database\Console\Seeds\WithoutModelEvents; // Baris ini di-comment karena tidak digunakan

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Fungsi ini digunakan untuk mengisi data permissions ke dalam database.
     */
    public function run(): void
    {
        // Permissions untuk User
        Permission::create(['name' => 'users index', 'guard_name' => 'web']); // Permission untuk melihat daftar users
        Permission::create(['name' => 'users create', 'guard_name' => 'web']); // Permission untuk membuat user baru
        Permission::create(['name' => 'users edit', 'guard_name' => 'web']); // Permission untuk mengedit user
        Permission::create(['name' => 'users delete', 'guard_name' => 'web']); // Permission untuk menghapus user

        // Permissions untuk Roles
        Permission::create(['name' => 'roles index', 'guard_name' => 'web']); // Permission untuk melihat daftar roles
        Permission::create(['name' => 'roles create', 'guard_name' => 'web']); // Permission untuk membuat role baru
        Permission::create(['name' => 'roles edit', 'guard_name' => 'web']); // Permission untuk mengedit role
        Permission::create(['name' => 'roles delete', 'guard_name' => 'web']); // Permission untuk menghapus role

        // Permissions untuk Permissions (hak akses terhadap permissions lainnya)
        Permission::create(['name' => 'permissions index', 'guard_name' => 'web']); // Permission untuk melihat daftar permissions
        Permission::create(['name' => 'permissions create', 'guard_name' => 'web']); // Permission untuk membuat permission baru
        Permission::create(['name' => 'permissions edit', 'guard_name' => 'web']); // Permission untuk mengedit permission
        Permission::create(['name' => 'permissions delete', 'guard_name' => 'web']); // Permission untuk menghapus permission
    }
}
