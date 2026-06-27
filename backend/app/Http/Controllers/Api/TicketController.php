<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\TicketRequest;
use App\Models\Ticket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController
{
    public function index(Request $request): JsonResponse
    {
        $query = Ticket::with(['requester', 'assignee', 'organization']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->has('assignee_id')) {
            $query->where('assignee_id', $request->assignee_id);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $tickets = $query->get();

        return response()->json($tickets);
    }

    public function store(TicketRequest $request): JsonResponse
    {
        $ticket = Ticket::create([
            'organization_id' => Auth::user()->organization_id,
            'requester_id' => Auth::id(),
            'subject' => $request->subject,
            'description' => $request->description,
            'status' => $request->status ?? 'open',
            'priority' => $request->priority ?? 'medium',
            'assignee_id' => $request->assignee_id,
        ]);

        return response()->json($ticket->load(['requester', 'assignee', 'organization']), 201);
    }

    public function show(Ticket $ticket): JsonResponse
    {
        return response()->json($ticket->load(['requester', 'assignee', 'organization']));
    }

    public function update(TicketRequest $request, Ticket $ticket): JsonResponse
    {
        $ticket->update($request->only([
            'subject',
            'description',
            'status',
            'priority',
            'assignee_id',
        ]));

        return response()->json($ticket->fresh()->load(['requester', 'assignee', 'organization']));
    }

    public function destroy(Ticket $ticket): JsonResponse
    {
        $ticket->delete();

        return response()->json(['message' => 'Ticket deleted'], 204);
    }
}
