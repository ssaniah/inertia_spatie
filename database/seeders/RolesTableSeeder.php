<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role; // Mengimpor model Role dari package Spatie untuk mengelola roles
use Illuminate\Database\Console\Seeds\WithoutModelEvents; // Baris ini di-comment karena tidak digunakan

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Fungsi ini digunakan untuk menambah data roles (peran) ke dalam database.
     */
    public function run(): void
    {
        // Membuat role dengan nama 'admin'
        Role::create(['name' => 'admin']); // Admin memiliki akses penuh terhadap aplikasi

        // Membuat role dengan nama 'user'
        Role::create(['name' => 'user']); // User adalah role dengan akses terbatas
    }
}
