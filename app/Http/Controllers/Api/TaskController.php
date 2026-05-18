<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Task\StoreTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Http\Requests\Task\UpdateTaskStatusRequest;
use App\Models\Project;
use App\Models\Task;
use App\Services\TaskService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    use ApiResponse;

    public function __construct(private TaskService $taskService) {}

    public function index(Request $request, int $projectId): JsonResponse
    {
        $user = $request->user();
        $project = Project::find($projectId);

        if (!$project) {
            return $this->notFoundResponse('Project not found');
        }

        $tasks = $this->taskService->getTasksForProject($projectId, $user);

        return $this->successResponse($tasks);
    }

    public function store(StoreTaskRequest $request, int $projectId): JsonResponse
    {
        $project = Project::find($projectId);

        if (!$project) {
            return $this->notFoundResponse('Project not found');
        }

        $task = $this->taskService->createTask(
            $request->validated(),
            $projectId,
            $request->user()
        );

        return $this->createdResponse($task, 'Task created successfully');
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        $task = $this->taskService->findTaskForUser($id, $user);

        if (!$task) {
            return $this->notFoundResponse('Task not found');
        }

        return $this->successResponse($task);
    }

    public function update(UpdateTaskRequest $request, int $id): JsonResponse
    {
        $user = $request->user();
        $task = Task::find($id);

        if (!$task) {
            return $this->notFoundResponse('Task not found');
        }

        // Members can only update their own assigned tasks
        if (!$user->isAdmin() && $task->assigned_to !== $user->id) {
            return $this->forbiddenResponse('You can only update your assigned tasks');
        }

        $updated = $this->taskService->updateTask($task, $request->validated(), $user);

        if (isset($updated['error'])) {
            return $this->errorResponse($updated['error'], 403);
        }

        return $this->successResponse($updated, 'Task updated successfully');
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $task = Task::find($id);

        if (!$task) {
            return $this->notFoundResponse('Task not found');
        }

        $task->delete();

        return $this->successResponse(null, 'Task deleted successfully');
    }

    public function updateStatus(UpdateTaskStatusRequest $request, int $id): JsonResponse
    {
        $user = $request->user();
        $task = Task::find($id);

        if (!$task) {
            return $this->notFoundResponse('Task not found');
        }

        // Members can only update their own tasks
        if (!$user->isAdmin() && $task->assigned_to !== $user->id) {
            return $this->forbiddenResponse('You can only update your assigned tasks');
        }

        $result = $this->taskService->updateStatus($task, $request->status, $user);

        if (isset($result['error'])) {
            return $this->errorResponse($result['error'], 403);
        }

        return $this->successResponse($result, 'Task status updated successfully');
    }

    public function myTasks(Request $request): JsonResponse
    {
        $user = $request->user();

        $tasks = Task::with(['project', 'assignee', 'creator'])
            ->where('assigned_to', $user->id)
            ->orderBy('due_date')
            ->get();

        return $this->successResponse($tasks);
    }
}
