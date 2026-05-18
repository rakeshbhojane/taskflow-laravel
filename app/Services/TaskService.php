<?php

namespace App\Services;

use App\Models\Task;
use App\Models\User;

class TaskService
{
    public function getTasksForProject(int $projectId, User $user): array
    {
        $query = Task::with(['assignee:id,name,email', 'creator:id,name,email'])
            ->where('project_id', $projectId);

        // Members only see their own tasks
        if (!$user->isAdmin()) {
            $query->where('assigned_to', $user->id);
        }

        return $query->orderBy('due_date')->get()->toArray();
    }

    public function createTask(array $data, int $projectId, User $creator): array
    {
        $task = Task::create([
            ...$data,
            'project_id' => $projectId,
            'created_by' => $creator->id,
            'status' => Task::STATUS_TODO,
        ]);

        return $task->load(['assignee:id,name,email', 'creator:id,name,email', 'project:id,name'])->toArray();
    }

    public function findTaskForUser(int $id, User $user): ?array
    {
        $query = Task::with(['assignee:id,name,email', 'creator:id,name,email', 'project:id,name']);

        if (!$user->isAdmin()) {
            $query->where('assigned_to', $user->id);
        }

        return $query->find($id)?->toArray();
    }

    public function updateTask(Task $task, array $data, User $user): array
    {
        // Validate status change if status is being updated
        if (isset($data['status'])) {
            $result = $this->validateStatusTransition($task, $data['status'], $user);
            if (isset($result['error'])) {
                return $result;
            }
        }

        $task->update($data);

        return $task->load(['assignee:id,name,email', 'creator:id,name,email', 'project:id,name'])->toArray();
    }

    public function updateStatus(Task $task, string $newStatus, User $user): array
    {
        $result = $this->validateStatusTransition($task, $newStatus, $user);

        if (isset($result['error'])) {
            return $result;
        }

        $task->update(['status' => $newStatus]);

        return $task->load(['assignee:id,name,email', 'creator:id,name,email', 'project:id,name'])->toArray();
    }

    private function validateStatusTransition(Task $task, string $newStatus, User $user): array
    {
        $currentStatus = $task->status;

        // Overdue tasks cannot move back to WIP/IN_PROGRESS
        if ($currentStatus === Task::STATUS_OVERDUE && $newStatus === Task::STATUS_WIP) {
            return ['error' => 'Overdue tasks cannot be moved back to WIP'];
        }

        // Only admin can close (mark DONE) overdue tasks
        if ($currentStatus === Task::STATUS_OVERDUE && $newStatus === Task::STATUS_DONE && !$user->isAdmin()) {
            return ['error' => 'Only administrators can close overdue tasks'];
        }

        return ['ok' => true];
    }

    public function markOverdueTasks(): int
    {
        $count = Task::whereNotIn('status', [Task::STATUS_DONE, Task::STATUS_OVERDUE])
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', now()->toDateString())
            ->update(['status' => Task::STATUS_OVERDUE]);

        return $count;
    }
}
