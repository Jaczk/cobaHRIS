<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Role;
use App\Models\Team;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Responsibility;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        //\App\Models\User::factory(2)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        //Company::factory(10)->create();

        //Role::factory(38)->create();

        //Responsibility::factory(125)->create();

        Employee::factory(400)->create();
    }
}
