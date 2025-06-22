<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Permission::all());
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'       => 'required|string|unique:permissions,name',
            'guard_name' => 'nullable|string',
        ]);
        $permission = Permission::create([
            'name'       => $data['name'],
            'guard_name' => $data['guard_name'] ?? 'web',
        ]);

        return response()->json($permission, 201);
    }

    public function show($id): JsonResponse
    {
        $permission = Permission::query()->findOrFail($id);

        return response()->json($permission);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $permission = Permission::query()->findOrFail($id);
        $data = $request->validate([
            'name'       => 'sometimes|required|string|unique:permissions,name,'.$permission->id,
            'guard_name' => 'nullable|string',
        ]);
        $permission->update([
            'name'       => $data['name'] ?? $permission->name,
            'guard_name' => $data['guard_name'] ?? $permission->guard_name,
        ]);

        return response()->json($permission);
    }

    public function destroy($id): JsonResponse
    {
        $permission = Permission::query()->findOrFail($id);
        $permission->delete();

        return response()->json(null, 204);
    }
}
