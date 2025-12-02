<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Gate;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        return $request->user()->projects()->get();
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100|unique:projects',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $project = new Project([
            'name' => $request->name,
            'owner_id' => $request->user()->id,
        ]);

        $project->save();
        $project->users()->attach($request->user()->id, ['role' => 'owner']);

        return response()->json($project, 201);
    }

    public function show(Request $request, Project $project)
    {
        return $project->load('users', 'tasks');
    }

    public function update(Request $request, Project $project)
    {
        if ($project->owner_id !== $request->user()->id) {
            return response()->json(['message' => 'Only the owner can update this project'], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:100|unique:projects,name,' . $project->id,
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $project->update($request->only('name'));
        return response()->json($project, 200);
    }

    public function destroy(Request $request, Project $project)
    {
        if ($project->owner_id !== $request->user()->id) {
            return response()->json(['message' => 'Only the owner can delete this project'], 403);
        }
        
        $project->delete();
        return response()->json(null, 204);
    }
}
