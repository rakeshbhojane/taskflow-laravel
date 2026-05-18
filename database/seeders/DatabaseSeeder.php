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
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@taskmanager.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        // Create regular users
        $user1 = User::create([
            'name' => 'John Doe',
            'email' => 'user@taskmanager.com',
            'password' => Hash::make('password123'),
            'role' => 'member',
        ]);

        $user2 = User::create([
            'name' => 'Jane Smith',
            'email' => 'jane@taskmanager.com',
            'password' => Hash::make('password123'),
            'role' => 'member',
        ]);

        // Create projects
        $project1 = Project::create([
            'name' => 'Website Redesign',
            'description' => 'Complete overhaul of company website with modern design',
            'status' => 'active',
            'created_by' => $admin->id,
        ]);

        $project2 = Project::create([
            'name' => 'Mobile App Development',
            'description' => 'Build iOS and Android apps for our platform',
            'status' => 'active',
            'created_by' => $admin->id,
        ]);

        // Add members to projects
        $project1->members()->attach([$admin->id, $user1->id, $user2->id]);
        $project2->members()->attach([$admin->id, $user2->id]);

        // Create tasks for project 1
        Task::create([
            'title' => 'Design Homepage Mockup',
            'description' => 'Create wireframes and high-fidelity mockups for the new homepage',
            'status' => 'DONE',
            'priority' => 'high',
            'due_date' => now()->addDays(7)->toDateString(),
            'project_id' => $project1->id,
            'assigned_to' => $user1->id,
            'created_by' => $admin->id,
        ]);

        Task::create([
            'title' => 'Implement Responsive Navigation',
            'description' => 'Build the navigation component that works on all screen sizes',
            'status' => 'WIP',
            'priority' => 'high',
            'due_date' => now()->addDays(5)->toDateString(),
            'project_id' => $project1->id,
            'assigned_to' => $user1->id,
            'created_by' => $admin->id,
        ]);

        Task::create([
            'title' => 'Write SEO Content',
            'description' => 'Optimize all page content for search engines',
            'status' => 'TODO',
            'priority' => 'medium',
            'due_date' => now()->addDays(14)->toDateString(),
            'project_id' => $project1->id,
            'assigned_to' => $user2->id,
            'created_by' => $admin->id,
        ]);

        Task::create([
            'title' => 'Fix Legacy CSS Issues',
            'description' => 'Clean up old CSS conflicts before launch',
            'status' => 'OVERDUE',
            'priority' => 'medium',
            'due_date' => now()->subDays(3)->toDateString(),
            'project_id' => $project1->id,
            'assigned_to' => $user1->id,
            'created_by' => $admin->id,
        ]);

        // Create tasks for project 2
        Task::create([
            'title' => 'API Architecture Design',
            'description' => 'Define the REST API endpoints and data models',
            'status' => 'DONE',
            'priority' => 'high',
            'due_date' => now()->addDays(3)->toDateString(),
            'project_id' => $project2->id,
            'assigned_to' => $user2->id,
            'created_by' => $admin->id,
        ]);

        Task::create([
            'title' => 'User Authentication Flow',
            'description' => 'Implement login, registration, and password reset',
            'status' => 'WIP',
            'priority' => 'high',
            'due_date' => now()->addDays(10)->toDateString(),
            'project_id' => $project2->id,
            'assigned_to' => $user2->id,
            'created_by' => $admin->id,
        ]);
    }
}
