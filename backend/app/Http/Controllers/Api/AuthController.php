<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Register new user with corporate
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'corporate_name' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'business_unit_name' => 'required|string|max:255',
        ]);

        // Create Corporate (Level 0)
        $corporate = Group::create([
            'name' => $request->corporate_name,
            'code' => 'CORP-' . strtoupper(substr(md5(uniqid()), 0, 8)),
            'type' => 'corporate',
            'level' => 0,
            'is_active' => true,
        ]);

        // Create Company (Level 1)
        $company = Group::create([
            'name' => $request->company_name,
            'code' => 'COMP-' . strtoupper(substr(md5(uniqid()), 0, 8)),
            'type' => 'company',
            'level' => 1,
            'parent_id' => $corporate->id,
            'tenant_id' => $corporate->id,
            'is_active' => true,
        ]);

        // Create Business Unit (Level 2)
        $businessUnit = Group::create([
            'name' => $request->business_unit_name,
            'code' => 'BU-' . strtoupper(substr(md5(uniqid()), 0, 8)),
            'type' => 'business_unit',
            'level' => 2,
            'parent_id' => $company->id,
            'tenant_id' => $corporate->id,
            'is_active' => true,
        ]);

        // Create User
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'tenant_id' => $corporate->id,
            'is_active' => true,
            'is_super_admin' => true, // First user is super admin
        ]);

        // Attach user to business unit
        $user->groups()->attach($businessUnit->id, [
            'is_primary' => true,
            'role_ids' => json_encode([]), // Will be set when roles are created
        ]);

        // Create token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Registration successful',
            'user' => $user,
            'corporate' => $corporate,
            'company' => $company,
            'business_unit' => $businessUnit,
            'token' => $token,
        ], 201);
    }

    /**
     * Login user
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        if (!$user->is_active) {
            throw ValidationException::withMessages([
                'email' => ['Your account is inactive. Please contact support.'],
            ]);
        }

        // Delete old tokens
        $user->tokens()->delete();

        // Create new token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user' => $user->load(['tenant', 'groups']),
            'token' => $token,
        ]);
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }

    /**
     * Get current user
     */
    public function me(Request $request)
    {
        $user = $request->user()->load([
            'tenant',
            'groups.children',
            'groups.parent',
        ]);

        return response()->json([
            'user' => $user,
            'permissions' => $user->getAllPermissions()->pluck('slug'),
            'roles' => $user->getAllRoles()->pluck('slug'),
        ]);
    }

    /**
     * Refresh token
     */
    public function refresh(Request $request)
    {
        $user = $request->user();
        
        // Delete old tokens
        $user->tokens()->delete();

        // Create new token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Token refreshed successfully',
            'token' => $token,
        ]);
    }
}
