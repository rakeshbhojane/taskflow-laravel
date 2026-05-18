<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    use ApiResponse;

    public function index(): JsonResponse
    {
        $users = User::select('id', 'name', 'email', 'role', 'created_at')
            ->orderBy('name')
            ->get();

        return $this->successResponse($users);
    }
}
