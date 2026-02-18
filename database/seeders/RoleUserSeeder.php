<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Super Admin',
                'need_approval' => false,
            ],
            [
                'name' => 'Guru',
                'need_approval' => false,
            ],
            [
                'name' => 'Kesiswaan',
                'need_approval' => false,
            ],
            [
                'name' => 'Siswa',
                'need_approval' => false,
            ],
        ];

        foreach ($roles as $role) {
            if (! DB::table('role_users')->where('name', '=', $role['name'])->exists()) {
                DB::table('role_users')->insert([
                    'id'    => uniqid(),
                    'name' => $role['name'],
                    'need_approval' => $role['need_approval'],
                    'author_id' => null,
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                ]);
            }
        }
    }
}
