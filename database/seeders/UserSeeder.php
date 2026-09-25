<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $petugas = [
            [
                'name'       => 'Petugas Loket',
                'email'      => 'loket@minapadi.com',
                'password'   => Hash::make('password123'),
                'role'       => 'loket',
                'aktif'      => true,
                'created_by' => 1, // ID admin
            ],
            [
                'name'       => 'Petugas Tubing Mini',
                'email'      => 'tubingmini@minapadi.com',
                'password'   => Hash::make('password123'),
                'role'       => 'tubing_mini',
                'aktif'      => true,
                'created_by' => 1,
            ],
            [
                'name'       => 'Petugas Tubing Dewasa',
                'email'      => 'tubingdewasa@minapadi.com',
                'password'   => Hash::make('password123'),
                'role'       => 'tubing_dewasa',
                'aktif'      => true,
                'created_by' => 1,
            ],
            [
                'name'       => 'Petugas Kolam',
                'email'      => 'kolam@minapadi.com',
                'password'   => Hash::make('password123'),
                'role'       => 'kolam',
                'aktif'      => true,
                'created_by' => 1,
            ],
            [
                'name'       => 'Petugas Kuliner',
                'email'      => 'kuliner@minapadi.com',
                'password'   => Hash::make('password123'),
                'role'       => 'kuliner',
                'aktif'      => true,
                'created_by' => 1,
            ],
        ];

        foreach ($petugas as $data) {
            // firstOrCreate supaya tidak duplikat kalau seeder dijalankan ulang
            User::firstOrCreate(
                ['email' => $data['email']],
                $data
            );
        }
    }
}