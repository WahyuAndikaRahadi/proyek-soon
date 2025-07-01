<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // Import DB Facade
use Illuminate\Support\Facades\Hash; // Import Hash Facade untuk password

class SupervisorRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Tambahkan Role 'supervisor' ke tabel 'roles' (jika ada tabel roles terpisah)
        // Jika Anda tidak memiliki tabel 'roles' terpisah, abaikan bagian ini atau sesuaikan.
        // Asumsi di sini bahwa Anda menggunakan kolom 'role' langsung di tabel 'users'.
        // Namun, jika Anda memiliki tabel 'roles', pastikan untuk menambahkan entri 'supervisor' di sini.
        // Contoh jika Anda punya tabel 'roles':
        // DB::table('roles')->updateOrInsert(
        //     ['name' => 'supervisor'],
        //     ['name' => 'supervisor', 'created_at' => now(), 'updated_at' => now()]
        // );
        // $this->command->info('Role "supervisor" added to roles table (if exists).');


        // 2. Buat contoh user supervisor
        // Pastikan Anda sudah menjalankan migrasi yang menambahkan 'supervisor' ke enum 'role' di tabel 'users'.
        DB::table('users')->updateOrInsert(
            ['email' => 'supervisor@example.com'], // Kunci unik untuk mencegah duplikasi
            [
                'name' => 'Supervisor',
                'email' => 'supervisor@jmk.com',
                'password' => Hash::make('password'), // Ganti dengan password yang lebih kuat di produksi
                'role' => 'supervisor', // Tetapkan role 'supervisor'
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $this->command->info('Supervisor user "supervisor@example.com" created/updated successfully.');
    }
}
