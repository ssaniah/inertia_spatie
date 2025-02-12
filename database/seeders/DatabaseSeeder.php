<?php

namespace Database\Seeders;

use App\Models\User; // Mengimpor model User, meskipun tidak digunakan dalam kode ini.
// use Illuminate\Database\Console\Seeds\WithoutModelEvents; // Baris ini di-comment karena tidak digunakan.

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Fungsi run ini akan digunakan untuk memanggil seeder lain dan menambahkan data ke database.
     */
    public function run(): void
    {
        // Memanggil seeder untuk tabel roles, permissions, dan users
        $this->call(RolesTableSeeder::class); // Seeder ini digunakan untuk menambah data ke tabel roles
        $this->call(PermissionsTableSeeder::class); // Seeder ini digunakan untuk menambah data ke tabel permissions
        $this->call(UserTableSeeder::class); // Seeder ini digunakan untuk menambah data ke tabel users
    }
}
