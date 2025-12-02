<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use App\Events\TaskCreated;
use App\Events\TaskUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class TaskController extends Controller
{
    public function index(Request $request, Project $project)
    {
        $query = $project->tasks();

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->has('assignee_id')) {
            $query->where('assignee_id', $request->assignee_id);
        }
        
        return $query->with('comments')->get();
    }

    public function store(Request $request, Project $project)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:100',
            'description' => 'required|string',
            'assignee_id' => 'required|exists:users,id',
            'status' => 'required|string',
            'priority' => 'required|integer|min:1|max:5',
            'due_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if (!$project->users->contains($request->assignee_id)) {
            return response()->json(['message' => 'Assignee is not a member of this project'], 422);
        }

        $task = $project->tasks()->create([
            'title' => $request->title,
            'description' => $request->description,
            'author_id' => $request->user()->id,
            'assignee_id' => $request->assignee_id,
            'status' => $request->status,
            'priority' => $request->priority,
            'due_date' => $request->due_date,
        ]);

        TaskCreated::dispatch($task);

        return response()->json($task, 201);
    }

    public function show(Request $request, Task $task)
    {
        if (!$task->project->users->contains($request->user()->id)) {
            return response()->json(['message' => 'Access denied'], 403);
        }
        
        return $task->load('comments');
    }

    public function update(Request $request, Task $task)
    {
        $user = $request->user();
        if ($task->author_id !== $user->id && $task->project->owner_id !== $user->id) {
            return response()->json(['message' => 'Access denied'], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:100',
            'description' => 'sometimes|required|string',
            'assignee_id' => 'sometimes|required|exists:users,id',
            'status' => 'sometimes|required|string', 
            'priority' => 'sometimes|required|integer|min:1|max:5',
            'due_date' => 'sometimes|required|date',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        
        $task->update($request->all());

        TaskUpdated::dispatch($task);

        return response()->json($task, 200);
    }

    public function destroy(Request $request, Task $task)
    {
        $user = $request->user();
        if ($task->author_id !== $user->id && $task->project->owner_id !== $user->id) {
            return response()->json(['message' => 'Access denied'], 403);
        }

        $task->delete();
        return response()->json(null, 204);
    }
}
