<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Role::all());
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'          => 'required|string|unique:roles,name',
            'guard_name'    => 'nullable|string',
            'permissions'   => 'array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);
        $role = Role::create([
            'name'       => $data['name'],
            'guard_name' => $data['guard_name'] ?? 'web',
        ]);
        if (! empty($data['permissions'])) {
            $role->syncPermissions($data['permissions']);
        }

        return response()->json($role, 201);
    }

    public function show($id): JsonResponse
    {
        $role = Role::with('permissions')->findOrFail($id);

        return response()->json($role);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $role = Role::query()->findOrFail($id);
        $data = $request->validate([
            'name'          => 'sometimes|required|string|unique:roles,name,'.$role->id,
            'guard_name'    => 'nullable|string',
            'permissions'   => 'array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);
        $role->update([
            'name'       => $data['name'] ?? $role->name,
            'guard_name' => $data['guard_name'] ?? $role->guard_name,
        ]);
        if (isset($data['permissions'])) {
            $role->syncPermissions($data['permissions']);
        }

        return response()->json($role);
    }

    public function destroy($id): JsonResponse
    {
        $role = Role::query()->findOrFail($id);
        $role->delete();

        return response()->json(null, 204);
    }
}
