<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FeedbackController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subject' => 'nullable|string|max:150',
            'anonymous' => 'boolean',
            'contact_email' => 'nullable|email|max:255',
            'message' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = $request->user();

        $feedback = Feedback::create([
            'user_id' => $user->id,
            'subject' => $request->input('subject'),
            'anonymous' => (bool) $request->input('anonymous', false),
            'contact_email' => $request->boolean('anonymous') ? null : $request->input('contact_email'),
            'message' => $request->input('message'),
        ]);

        return response()->json([
            'message' => 'Feedback submitted successfully',
            'feedback' => $feedback,
        ], 201);
    }

    public function index(Request $request)
    {
        $actor = $request->user();
        if (!in_array($actor->role ?? 'citizen', ['admin','mayor'])) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $query = Feedback::query();

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->query('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->query('date_to'));
        }

        $perPage = (int) $request->query('per_page', 10);
        $perPage = max(1, min(100, $perPage));

        $items = $query->orderByDesc('created_at')->paginate($perPage);
        return response()->json($items);
    }

    public function mine(Request $request)
    {
        $items = Feedback::where('user_id', $request->user()->id)
            ->orderByDesc('created_at')
            ->get();
        return response()->json(['feedback' => $items]);
    }

    public function show(Feedback $feedback, Request $request)
    {
        $actor = $request->user();
        $role = $actor->role ?? 'citizen';
        if ($feedback->user_id !== $actor->id && !in_array($role, ['admin','mayor'])) {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        return response()->json(['feedback' => $feedback]);
    }
}


