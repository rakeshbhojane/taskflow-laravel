<?php

namespace App\Http\Requests\Task;

use App\Models\Task;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Further auth handled in controller
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'priority' => ['sometimes', Rule::in([Task::PRIORITY_LOW, Task::PRIORITY_MEDIUM, Task::PRIORITY_HIGH])],
            'due_date' => ['sometimes', 'date'],
            'assigned_to' => ['sometimes', 'exists:users,id'],
            'status' => ['sometimes', Rule::in([Task::STATUS_TODO, Task::STATUS_WIP, Task::STATUS_DONE, Task::STATUS_OVERDUE])],
        ];
    }
}
