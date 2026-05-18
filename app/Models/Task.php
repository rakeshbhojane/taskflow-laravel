<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    const STATUS_TODO = 'TODO';
    const STATUS_WIP = 'WIP';
    const STATUS_DONE = 'DONE';
    const STATUS_OVERDUE = 'OVERDUE';

    const PRIORITY_LOW = 'low';
    const PRIORITY_MEDIUM = 'medium';
    const PRIORITY_HIGH = 'high';

    protected $fillable = [
        'title',
        'description',
        'status',
        'priority',
        'due_date',
        'project_id',
        'assigned_to',
        'created_by',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isOverdue(): bool
    {
        return $this->status === self::STATUS_OVERDUE;
    }

    public function isDone(): bool
    {
        return $this->status === self::STATUS_DONE;
    }

    public function isPastDue(): bool
    {
        return $this->due_date && $this->due_date->isPast() && !$this->isDone();
    }
}
