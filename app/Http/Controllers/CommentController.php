<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Events\CommentCreated;

class CommentController extends Controller
{
    public function index(Request $request, Task $task)
    {
        if (!$task->project->users->contains($request->user()->id)) {
             return response()->json(['message' => 'Access denied'], 403);
        }
        
        return $task->comments()->with('user')->get();
    }

    public function store(Request $request, Task $task)
    {
        if (!$task->project->users->contains($request->user()->id)) {
            return response()->json(['message' => 'Access denied'], 403);
        }

        $validator = Validator::make($request->all(), [
            'body' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        
        $comment = $task->comments()->create([
            'body' => $request->body,
            'author_id' => $request->user()->id
        ]);
        
        CommentCreated::dispatch($comment);

        return response()->json($comment->load('user'), 201);
    }

    public function destroy(Request $request, Comment $comment)
    {
        if ($comment->author_id !== $request->user()->id) {
             return response()->json(['message' => 'Access denied'], 403);
        }
        
        $comment->delete();
        return response()->json(null, 204);
    }
}
