<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $acme = Organization::create([
            'name' => 'Acme',
            'slug' => 'acme',
        ]);

        User::create([
            'name' => 'Acme Admin',
            'email' => 'admin@acme.test',
            'password' => Hash::make('password'),
            'organization_id' => $acme->id,
            'role' => Role::Admin->value,
        ]);

        User::create([
            'name' => 'Acme Agent',
            'email' => 'agent@acme.test',
            'password' => Hash::make('password'),
            'organization_id' => $acme->id,
            'role' => Role::Agent->value,
        ]);

        User::create([
            'name' => 'Acme Customer',
            'email' => 'customer@acme.test',
            'password' => Hash::make('password'),
            'organization_id' => $acme->id,
            'role' => Role::Customer->value,
        ]);
    }
}
