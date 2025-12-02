<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Project;
use App\Models\Report;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class GenerateReport extends Command
{
    protected $signature = 'app:generate-report';
    protected $description = 'Збирає статистику по задачам та зберігає звіт';

    public function handle()
    {
        $this->info('Початок генерації звіту...');

        $projects = Project::with('tasks')->get();
        $reportData = [];

        foreach ($projects as $project) {
            $stats = [
                'project_id' => $project->id,
                'project_name' => $project->name,
                'todo' => $project->tasks->where('status', 'new')->count(),
                'in_progress' => $project->tasks->whereIn('status', ['in_progress', 'review'])->count(),
                'done' => $project->tasks->where('status', 'done')->count(),
                'expired' => $project->tasks->where('due_date', '<', now())->where('status', '!=', 'done')->count(),
            ];

            $reportData[] = $stats;
        }

        $payload = json_encode($reportData, JSON_PRETTY_PRINT);
        $fileName = 'report_' . date('Y-m-d_H-i-s') . '.json';
        $path = 'reports/' . $fileName;

        Storage::disk('local')->put($path, $payload);

        Report::create([
            'period_start' => Carbon::now()->startOfMonth(),
            'period_end' => Carbon::now(),
            'payload' => $payload,
            'path' => $path
        ]);

        $this->info("Звіт успішно згенеровано! Шлях: storage/app/private/$path");
    }
}
