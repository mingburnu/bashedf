<?php

namespace Database\Seeders;

use Carbon\Carbon;
use DB;
use Hash;
use Illuminate\Database\Seeder;
use Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        if (!DB::table('admins')->exists()) {
            $model_type = 'Admin';
            $model_id = DB::table('admins')->insertGetId([
                'name' => Str::random(10),
                'email' => 'admin@test.com',
                'password' => Hash::make('Pa55w0rd'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            $role_id = DB::table(config('permission.table_names.roles'))->insertGetId(
                [
                    'name' => 'god',
                    'guard_name' => 'admin',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);

            DB::table(config('permission.table_names.model_has_roles'))->insert(compact('role_id', 'model_type', 'model_id'));

            $model_id = DB::table('admins')->insertGetId([
                'name' => Str::random(10),
                'email' => 'reporter@test.com',
                'password' => Hash::make('Pa55w0rd'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            DB::table(config('permission.table_names.model_has_roles'))->insert(compact('role_id', 'model_type', 'model_id'));

            DB::table(config('permission.table_names.permissions'))->insert([
                [
                    'name' => 'admin',
                    'guard_name' => 'admin',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'name' => 'user',
                    'guard_name' => 'admin',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'name' => 'bank_card',
                    'guard_name' => 'admin',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'name' => 'new',
                    'guard_name' => 'admin',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'name' => 'payment',
                    'guard_name' => 'admin',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'name' => 'deposit',
                    'guard_name' => 'admin',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'name' => 'report',
                    'guard_name' => 'admin',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'name' => 'wallet',
                    'guard_name' => 'admin',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]
            ]);
        }
    }
}
