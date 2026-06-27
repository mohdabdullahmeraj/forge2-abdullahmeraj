<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\CommentRequest;
use App\Models\Comment;
use App\Models\Ticket;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class CommentController
{
    public function index(Ticket $ticket): JsonResponse
    {
        $query = Comment::where('ticket_id', $ticket->id)
            ->with(['author', 'children', 'children.author']);

        if (Auth::user()->isCustomer()) {
            $query->where('type', 'public');
        }

        $comments = $query->get();

        return response()->json($comments);
    }

    public function store(CommentRequest $request, Ticket $ticket): JsonResponse
    {
        $type = $request->type ?? 'public';

        if ($type === 'internal' && Auth::user()->isCustomer()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $comment = Comment::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'organization_id' => Auth::user()->organization_id,
            'body' => $request->body,
            'type' => $type,
            'parent_id' => $request->parent_id,
        ]);

        return response()->json($comment->load(['author', 'children', 'parent']), 201);
    }
}
