<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Group;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    /**
     * Get organization hierarchy
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Get tenant's complete hierarchy
        $corporate = Group::with(['children.children'])
            ->where('id', $user->tenant_id)
            ->first();

        return response()->json([
            'hierarchy' => $corporate,
        ]);
    }

    /**
     * Get specific group with details
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();
        
        $group = Group::with(['parent', 'children', 'users', 'roles'])
            ->forTenant($user->tenant_id)
            ->findOrFail($id);

        // Check if user can access this group
        if (!$user->canAccessGroup($group->id)) {
            return response()->json([
                'message' => 'Unauthorized access to this group',
            ], 403);
        }

        return response()->json([
            'group' => $group,
        ]);
    }

    /**
     * Create new group (Company or Business Unit)
     */
    public function store(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:company,business_unit',
            'parent_id' => 'required|exists:groups,id',
            'description' => 'nullable|string',
        ]);

        $parent = Group::forTenant($user->tenant_id)->findOrFail($request->parent_id);

        // Check if user can access parent group
        if (!$user->canAccessGroup($parent->id)) {
            return response()->json([
                'message' => 'Unauthorized access to parent group',
            ], 403);
        }

        // Validate hierarchy level
        $level = $parent->level + 1;
        if ($level > 2) {
            return response()->json([
                'message' => 'Maximum hierarchy level reached (3 levels max)',
            ], 400);
        }

        // Validate type based on parent
        if ($parent->type === 'business_unit') {
            return response()->json([
                'message' => 'Cannot create child under business unit',
            ], 400);
        }

        $group = Group::create([
            'name' => $request->name,
            'code' => strtoupper($request->type === 'company' ? 'COMP' : 'BU') . '-' . strtoupper(substr(md5(uniqid()), 0, 8)),
            'type' => $request->type,
            'level' => $level,
            'parent_id' => $parent->id,
            'tenant_id' => $user->tenant_id,
            'description' => $request->description,
            'is_active' => true,
        ]);

        return response()->json([
            'message' => 'Group created successfully',
            'group' => $group->load('parent'),
        ], 201);
    }

    /**
     * Update group
     */
    public function update(Request $request, $id)
    {
        $user = $request->user();

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
        ]);

        $group = Group::forTenant($user->tenant_id)->findOrFail($id);

        // Check if user can access this group
        if (!$user->canAccessGroup($group->id)) {
            return response()->json([
                'message' => 'Unauthorized access to this group',
            ], 403);
        }

        // Corporate cannot be deactivated
        if ($group->isCorporate() && $request->has('is_active') && !$request->is_active) {
            return response()->json([
                'message' => 'Corporate group cannot be deactivated',
            ], 400);
        }

        $group->update($request->only(['name', 'description', 'is_active']));

        return response()->json([
            'message' => 'Group updated successfully',
            'group' => $group,
        ]);
    }

    /**
     * Delete group
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->user();

        $group = Group::forTenant($user->tenant_id)->findOrFail($id);

        // Check if user can access this group
        if (!$user->canAccessGroup($group->id)) {
            return response()->json([
                'message' => 'Unauthorized access to this group',
            ], 403);
        }

        // Corporate cannot be deleted
        if ($group->isCorporate()) {
            return response()->json([
                'message' => 'Corporate group cannot be deleted',
            ], 400);
        }

        // Check if group has children
        if ($group->children()->exists()) {
            return response()->json([
                'message' => 'Cannot delete group with child groups',
            ], 400);
        }

        $group->delete();

        return response()->json([
            'message' => 'Group deleted successfully',
        ]);
    }

    /**
     * Get users in specific group
     */
    public function users(Request $request, $id)
    {
        $user = $request->user();

        $group = Group::forTenant($user->tenant_id)->findOrFail($id);

        // Check if user can access this group
        if (!$user->canAccessGroup($group->id)) {
            return response()->json([
                'message' => 'Unauthorized access to this group',
            ], 403);
        }

        $users = $group->users()->with('groups')->get();

        return response()->json([
            'users' => $users,
        ]);
    }
}
