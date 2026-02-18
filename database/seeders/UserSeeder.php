<?php

namespace Database\Seeders;

use App\Models\RoleUser;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@smkn1ciamis.sch.id',
                'nisn' => null,
                'role' => 'Super Admin',
                'password' => 'superadmin123',
            ],
            [
                'name' => 'Guru Demo',
                'email' => 'guru@smkn1ciamis.sch.id',
                'nisn' => null,
                'role' => 'Guru',
                'password' => 'guru123',
            ],
            [
                'name' => 'Kesiswaan Demo',
                'email' => 'kesiswaan@smkn1ciamis.sch.id',
                'nisn' => null,
                'role' => 'Kesiswaan',
                'password' => 'kesiswaan123',
            ],
            [
                'name' => 'Siswa Demo 1',
                'email' => 'siswa1@smkn1ciamis.sch.id',
                'nisn' => '0012345678',
                'role' => 'Siswa',
                'password' => 'siswa123',
            ],
            [
                'name' => 'Siswa Demo 2',
                'email' => 'siswa2@smkn1ciamis.sch.id',
                'nisn' => '0012345679',
                'role' => 'Siswa',
                'password' => 'siswa123',
            ],
            [
                'name' => 'Siswa Demo 3',
                'email' => 'siswa3@smkn1ciamis.sch.id',
                'nisn' => '0012345680',
                'role' => 'Siswa',
                'password' => 'siswa123',
            ],
        ];

        foreach ($users as $userData) {
            // Check by email or nisn
            $existsQuery = DB::table('users')->where('email', $userData['email']);
            if ($userData['nisn']) {
                $existsQuery->orWhere('nisn', $userData['nisn']);
            }

            if (!$existsQuery->exists()) {
                $roleId = RoleUser::where('name', $userData['role'])->first()?->id;

                if ($roleId) {
                    DB::table('users')->insert([
                        'id'    => uniqid(),
                        'name' => $userData['name'],
                        'email' => $userData['email'],
                        'nisn' => $userData['nisn'],
                        'role_user_id'  => $roleId,
                        'email_verified_at' => Carbon::now()->format('Y-m-d H:i:s'),
                        'password' => Hash::make($userData['password']),
                        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    ]);
                }
            }
        }
    }
}
