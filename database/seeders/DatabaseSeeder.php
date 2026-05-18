<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@taskmanager.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password123'),
                'role' => 'admin',
            ]
        );

        $user1 = User::firstOrCreate(
            ['email' => 'user@taskmanager.com'],
            [
                'name' => 'John Doe',
                'password' => Hash::make('password123'),
                'role' => 'member',
            ]
        );

        $user2 = User::firstOrCreate(
            ['email' => 'jane@taskmanager.com'],
            [
                'name' => 'Jane Smith',
                'password' => Hash::make('password123'),
                'role' => 'member',
            ]
        );

        $project1 = Project::firstOrCreate(
            ['name' => 'Website Redesign'],
            [
                'description' => 'Complete overhaul of company website',
                'status' => 'active',
                'created_by' => $admin->id,
            ]
        );

        $project2 = Project::firstOrCreate(
            ['name' => 'Mobile App Development'],
            [
                'description' => 'Build iOS and Android apps',
                'status' => 'active',
                'created_by' => $admin->id,
            ]
        );

        $project1->members()->syncWithoutDetaching([$admin->id, $user1->id, $user2->id]);
        $project2->members()->syncWithoutDetaching([$admin->id, $user2->id]);

        if (Task::count() === 0) {
            Task::create([
                'title' => 'Design Homepage Mockup',
                'description' => 'Create wireframes for the new homepage',
                'status' => 'DONE',
                'priority' => 'high',
                'due_date' => now()->addDays(7)->toDateString(),
                'project_id' => $project1->id,
                'assigned_to' => $user1->id,
                'created_by' => $admin->id,
            ]);

            Task::create([
                'title' => 'Implement Responsive Navigation',
                'description' => 'Build navigation component',
                'status' => 'WIP',
                'priority' => 'high',
                'due_date' => now()->addDays(5)->toDateString(),
                'project_id' => $project1->id,
                'assigned_to' => $user1->id,
                'created_by' => $admin->id,
            ]);

            Task::create([
                'title' => 'Fix Legacy CSS Issues',
                'description' => 'Clean up old CSS conflicts',
                'status' => 'OVERDUE',
                'priority' => 'medium',
                'due_date' => now()->subDays(3)->toDateString(),
                'project_id' => $project1->id,
                'assigned_to' => $user1->id,
                'created_by' => $admin->id,
            ]);

            Task::create([
                'title' => 'User Authentication Flow',
                'description' => 'Implement login and registration',
                'status' => 'WIP',
                'priority' => 'high',
                'due_date' => now()->addDays(10)->toDateString(),
                'project_id' => $project2->id,
                'assigned_to' => $user2->id,
                'created_by' => $admin->id,
            ]);
        }
    }
}