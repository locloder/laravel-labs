<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Project;

class CheckProjectAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $project = $request->route('project');

        if ($request->route('task')) {
            $project = $request->route('task')->project;
        }

        if (!$project) {
             $projectId = $request->route('id');
             if ($projectId) {
                $project = Project::find($projectId);
             }
        }

        if (!$project) {
            return response()->json(['message' => 'Project not found'], 404);
        }
        
        if (!$project->users->contains($user->id)) {
            return response()->json(['message' => 'Access denied to this project'], 403);
        }

        return $next($request);
    }
}
