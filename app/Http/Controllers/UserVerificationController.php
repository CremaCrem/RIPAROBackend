<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserVerificationController extends Controller
{
    public function index(Request $request)
    {
        $actor = $request->user();
        if (!in_array($actor->role ?? 'citizen', ['admin','mayor'])) {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        $status = $request->query('status'); // optional filter
        $q = User::query()->orderByDesc('created_at');
        if ($status) $q->where('verification_status', $status);
        // Avoid exposing sensitive fields
        $users = $q->get(['id','first_name','last_name','email','mobile_number','barangay','zone','verification_status','id_document_path','created_at']);
        return response()->json(['users' => $users]);
    }

    public function updateStatus(Request $request, User $user)
    {
        $actor = $request->user();
        if (!in_array($actor->role ?? 'citizen', ['admin','mayor'])) {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        $validator = Validator::make($request->all(), [
            'action' => 'required|in:verify,reject,pending',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed','errors'=>$validator->errors()], 422);
        }
        $action = $request->input('action');
        $map = [
            'verify' => 'verified',
            'reject' => 'rejected',
            'pending' => 'pending',
        ];
        $user->verification_status = $map[$action];
        $user->save();
        return response()->json(['message' => 'Updated','user'=>$user]);
    }
}
