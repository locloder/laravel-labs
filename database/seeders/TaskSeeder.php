<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Task;
use App\Models\Project;
use App\Models\User;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $projects = Project::all();
        $users = User::all();

        if ($projects->isEmpty() || $users->isEmpty()) {
            $this->command->warn('No projects or users found. Please run UserSeeder and ProjectSeeder first.');
            return;
        }

        foreach ($projects as $project) {
            Task::factory(rand(5, 10))->create([
                'project_id' => $project->id,
                'author_id' => $users->random()->id,
                'assignee_id' => $users->random()->id,
            ]);
        }
    }
}
