<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Comment;
use App\Models\Task;
use App\Models\User;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tasks = Task::all();
        $users = User::all();

        if ($tasks->isEmpty() || $users->isEmpty()) {
            $this->command->warn('No tasks or users found. Please run seeders in the correct order.');
            return;
        }

        foreach ($tasks as $task) {
            Comment::factory(rand(1, 4))->create([
                'task_id' => $task->id,
                'author_id' => $users->random()->id,
            ]);
        }
    }
}
