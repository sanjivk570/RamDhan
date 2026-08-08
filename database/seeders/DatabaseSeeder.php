<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $this->call([
            \App\Modules\Role\Seeders\RoleSeeder::class,
            \App\Modules\Role\Seeders\PermissionSeeder::class,
            \App\Modules\Role\Seeders\RolePermissionSeeder::class,
            \App\Modules\User\Seeders\SuperAdminSeeder::class,
            \App\Modules\Category\Seeders\CategorySeeder::class,
        ]);
    }
}
