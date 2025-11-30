<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'mobile_number' => 'required|string|max:20',
            'barangay' => 'nullable|string|max:100',
            'zone' => 'nullable|string|max:50',
            'id_document' => 'sometimes|file|image|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $idPath = null;
        if ($request->hasFile('id_document')) {
            $path = $request->file('id_document')->store('id_documents', 'public');
            $idPath = url(Storage::url($path));
        }

        $user = User::create([
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'mobile_number' => $request->mobile_number,
            'barangay' => $request->barangay,
            'zone' => $request->zone,
            'verification_status' => 'pending',
            'id_document_path' => $idPath,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user,
            'token' => $token
        ], 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Gate by verification_status
        if ($user->verification_status === 'pending') {
            return response()->json([
                'message' => 'Your account is still under review.',
                'status' => 'pending'
            ], 403);
        }
        if ($user->verification_status === 'rejected') {
            return response()->json([
                'message' => 'Your registration was rejected.',
                'status' => 'rejected'
            ], 403);
        }

        // Check if account is deactivated
        if (!$user->is_active) {
            return response()->json([
                'message' => 'Your account has been deactivated. Please contact customer support for assistance.',
                'status' => 'deactivated'
            ], 403);
        }

        // Update last_active_at on login
        $user->last_active_at = now();
        $user->save();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user' => $user,
            'token' => $token
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }
}
