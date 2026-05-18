<?php

namespace App\Services;

use App\Models\Project;
use App\Models\User;

class ProjectService
{
    public function getProjectsForUser(User $user): array
    {
        $query = Project::with(['creator:id,name,email', 'members:id,name,email'])
            ->withCount('tasks');

        if (!$user->isAdmin()) {
            $query->whereHas('members', fn($q) => $q->where('users.id', $user->id));
        }

        return $query->orderBy('created_at', 'desc')->get()->toArray();
    }

    public function createProject(array $data, User $creator): array
    {
        $project = Project::create([
            ...$data,
            'created_by' => $creator->id,
        ]);

        // Add creator as a member automatically
        $project->members()->attach($creator->id);

        return $project->load(['creator:id,name,email', 'members:id,name,email'])->toArray();
    }

    public function findProjectForUser(int $id, User $user): ?array
    {
        $query = Project::with([
            'creator:id,name,email',
            'members:id,name,email',
            'tasks' => fn($q) => $q->with(['assignee:id,name,email', 'creator:id,name,email']),
        ]);

        if (!$user->isAdmin()) {
            $query->whereHas('members', fn($q) => $q->where('users.id', $user->id));
        }

        $project = $query->find($id);

        return $project?->toArray();
    }

    public function updateProject(Project $project, array $data): array
    {
        $project->update($data);

        return $project->load(['creator:id,name,email', 'members:id,name,email'])->toArray();
    }
}
