<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Project\StoreProjectRequest;
use App\Http\Requests\Project\UpdateProjectRequest;
use App\Models\Project;
use App\Models\User;
use App\Services\ProjectService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    use ApiResponse;

    public function __construct(private ProjectService $projectService) {}

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $projects = $this->projectService->getProjectsForUser($user);

        return $this->successResponse($projects);
    }

    public function store(StoreProjectRequest $request): JsonResponse
    {
        $project = $this->projectService->createProject(
            $request->validated(),
            $request->user()
        );

        return $this->createdResponse($project, 'Project created successfully');
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        $project = $this->projectService->findProjectForUser($id, $user);

        if (!$project) {
            return $this->notFoundResponse('Project not found');
        }

        return $this->successResponse($project);
    }

    public function update(UpdateProjectRequest $request, int $id): JsonResponse
    {
        $project = Project::find($id);

        if (!$project) {
            return $this->notFoundResponse('Project not found');
        }

        $updated = $this->projectService->updateProject($project, $request->validated());

        return $this->successResponse($updated, 'Project updated successfully');
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $project = Project::find($id);

        if (!$project) {
            return $this->notFoundResponse('Project not found');
        }

        $project->delete();

        return $this->successResponse(null, 'Project deleted successfully');
    }

    public function members(Request $request, int $id): JsonResponse
    {
        $project = Project::with('members')->find($id);

        if (!$project) {
            return $this->notFoundResponse('Project not found');
        }

        return $this->successResponse($project->members);
    }

    public function addMember(Request $request, int $id): JsonResponse
    {
        $request->validate(['user_id' => 'required|exists:users,id']);

        $project = Project::find($id);

        if (!$project) {
            return $this->notFoundResponse('Project not found');
        }

        $project->members()->syncWithoutDetaching([$request->user_id]);

        return $this->successResponse(null, 'Member added successfully');
    }
}
