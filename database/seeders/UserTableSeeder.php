<?php

namespace Database\Seeders;

use App\Models\User; // Mengimpor model User untuk membuat dan mengelola user
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role; // Mengimpor model Role untuk mengelola role
use Spatie\Permission\Models\Permission; // Mengimpor model Permission untuk mengelola permissions
use Illuminate\Database\Console\Seeds\WithoutModelEvents; // Baris ini di-comment karena tidak digunakan

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Fungsi ini digunakan untuk membuat user baru, mengaitkan role dan permissions ke user.
     */
    public function run(): void
    {
        // Membuat user baru dengan nama, email, dan password yang sudah dienkripsi
        $user = User::create([
            'name'      => 'Saniah', // Nama pengguna
            'email'     => 'sani@gmail.com', // Alamat email pengguna
            'password'  => bcrypt('12345678'), // Password yang dienkripsi menggunakan bcrypt
        ]);

        // Mengambil semua permissions yang ada dalam tabel permissions
        $permissions = Permission::all(); // Mendapatkan semua permission yang sudah ada

        // Mengambil role dengan ID 1, diharapkan role admin memiliki ID 1
        $role = Role::find(1); // Mengambil role dengan ID 1 (biasanya role admin)

        // Menyinkronkan permissions yang ada dengan role yang dipilih
        $role->syncPermissions($permissions); // Menghubungkan role dengan semua permissions yang ada

        // Memberikan role yang telah dikaitkan dengan permissions ke user
        $user->assignRole($role); // Memberikan role 'admin' kepada user yang baru dibuat
    }
}
