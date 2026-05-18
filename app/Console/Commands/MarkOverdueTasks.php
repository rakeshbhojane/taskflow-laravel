<?php

namespace App\Console\Commands;

use App\Services\TaskService;
use Illuminate\Console\Command;

class MarkOverdueTasks extends Command
{
    protected $signature   = 'tasks:mark-overdue';
    protected $description = 'Mark tasks as OVERDUE when due_date is past and status is not DONE';

    public function handle(TaskService $taskService): int
    {
        $this->info('Checking for overdue tasks...');

        $count = $taskService->markOverdueTasks();

        $this->info("✅ Marked {$count} task(s) as OVERDUE.");

        return Command::SUCCESS;
    }
}
