<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class UserUpdateRequestController extends Controller
{
    public function index(Request $request)
    {
        $actor = $request->user();
        if (!in_array($actor->role ?? 'citizen', ['admin','mayor'])) {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        $status = $request->query('status');
        $q = UserUpdateRequest::query()->with(['user:id,first_name,last_name,email,mobile_number,barangay,zone,id_document_path']);
        if ($status) $q->where('status', $status);
        $items = $q->orderByDesc('created_at')->get();
        return response()->json(['requests' => $items]);
    }

    public function show(UserUpdateRequest $requestModel, Request $request)
    {
        $actor = $request->user();
        if (!in_array($actor->role ?? 'citizen', ['admin','mayor'])) {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        $requestModel->load(['user:id,first_name,last_name,email,mobile_number,barangay,zone,id_document_path']);
        return response()->json(['request' => $requestModel]);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'first_name' => 'nullable|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:255',
            'mobile_number' => 'nullable|string|max:20',
            'barangay' => 'nullable|string|max:100',
            'zone' => 'nullable|string|max:50',
            'password' => 'nullable|string|min:8',
            'id_document' => 'required|file|image|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        // require at least one field change besides the id_document
        $hasChange = false;
        foreach (['first_name','middle_name','last_name','email','mobile_number','barangay','zone','password'] as $field) {
            if ($request->filled($field)) { $hasChange = true; break; }
        }
        if (!$hasChange) {
            return response()->json(['message' => 'No changes provided'], 422);
        }

        $idPath = null;
        if ($request->hasFile('id_document')) {
            $path = $request->file('id_document')->store('id_documents', 'public');
            $idPath = url(Storage::url($path));
        }

        $payload = [
            'user_id' => $user->id,
            'first_name' => $request->input('first_name'),
            'middle_name' => $request->input('middle_name'),
            'last_name' => $request->input('last_name'),
            'email' => $request->input('email'),
            'mobile_number' => $request->input('mobile_number'),
            'barangay' => $request->input('barangay'),
            'zone' => $request->input('zone'),
            // Hash password immediately if provided, we will not display it back
            'password' => $request->filled('password') ? Hash::make($request->input('password')) : null,
            'id_document_path' => $idPath,
            'status' => 'pending',
        ];

        $req = UserUpdateRequest::create($payload);

        return response()->json(['message' => 'Update request submitted', 'request' => $req], 201);
    }

    public function review(UserUpdateRequest $requestModel, Request $request)
    {
        $actor = $request->user();
        if (!in_array($actor->role ?? 'citizen', ['admin','mayor'])) {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        $validator = Validator::make($request->all(), [
            'action' => 'required|in:approve,reject',
            'admin_notes' => 'nullable|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        if ($request->input('action') === 'approve') {
            // Apply changes to the user
            $user = User::findOrFail($requestModel->user_id);
            $fields = ['first_name','middle_name','last_name','email','mobile_number','barangay','zone'];
            foreach ($fields as $f) {
                if (!is_null($requestModel->$f) && $requestModel->$f !== '') {
                    $user->$f = $requestModel->$f;
                }
            }
            if (!is_null($requestModel->password) && $requestModel->password !== '') {
                // Already hashed on create
                $user->password = $requestModel->password;
            }
            if (!is_null($requestModel->id_document_path) && $requestModel->id_document_path !== '') {
                $user->id_document_path = $requestModel->id_document_path;
            }
            $user->save();

            $requestModel->status = 'approved';
            $requestModel->reviewed_by = $actor->id;
            $requestModel->reviewed_at = now();
            $requestModel->admin_notes = $request->input('admin_notes');
            $requestModel->save();

            return response()->json(['message' => 'Request approved', 'request' => $requestModel, 'user' => $user]);
        }

        // Reject
        $requestModel->status = 'rejected';
        $requestModel->reviewed_by = $actor->id;
        $requestModel->reviewed_at = now();
        $requestModel->admin_notes = $request->input('admin_notes');
        $requestModel->save();
        return response()->json(['message' => 'Request rejected', 'request' => $requestModel]);
    }
}


