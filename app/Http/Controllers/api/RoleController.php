<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AssignPermissionsRequest;
use App\Http\Requests\ChangeRoleStateRequest;
use App\Http\Requests\CreateRoleRequest;
use App\Http\Requests\GetRolePermissionsRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public Role $model;

    public function __construct(Role $role)
    {
        $this->model = $role;
    }

    /**
     * 角色列表
     * @param Request $request
     * @return string
     */
    public function list(Request $request): string
    {
        $params = $request->query();
        $data = $this->model->result($params);
        return json_encode(['code' => 0, 'message' => '', 'data' => $data], JSON_UNESCAPED_UNICODE);
    }

    /**
     * 创建角色
     * @param CreateRoleRequest $request
     * @return string
     */
    public function store(CreateRoleRequest $request): string
    {
        $result = ['code' => 0, 'message' => '保存成功'];
        list($success, $message) = $this->model->store($request);
        if (!$success) {
            $result['code'] = -1;
            $result['message'] = $message;
        }
        return json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    /**
     * 更新角色
     * @param UpdateRoleRequest $request
     * @return string
     */
    public function update(UpdateRoleRequest $request): string
    {
        $result = ['code' => 0, 'message' => '更新成功'];
        list($success, $message) = $this->model->updateRole($request);
        if (!$success) {
            $result['code'] = -1;
            $result['message'] = $message;
        }
        return json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    /**
     * 更改角色状态
     * @param ChangeRoleStateRequest $request
     * @return string
     */
    public function changeState(ChangeRoleStateRequest $request): string
    {
        $result = ['code' => 0, 'message' => '操作成功'];
        list($success, $message) = $this->model->changeState($request);
        if (!$success) {
            $result['code'] = -1;
            $result['message'] = $message;
        }
        return json_encode($result, JSON_UNESCAPED_UNICODE);
    }


    /**
     * 获取角色权限
     * @param GetRolePermissionsRequest $request
     * @return string
     */
    public function getRolePermissions(GetRolePermissionsRequest $request): string
    {
        $result = ['code' => 0, 'message' => '获取成功', 'data' => []];
        try {
            $roleId = $request->input('role_id');
            list($data, $checkedIds) = $this->model->getRolePermissions($roleId);
            $result['data']['data'] = $data;
            $result['data']['permissions'] = $checkedIds;
        } catch (\Exception $e) {
            $result['code'] = -1;
            $result['message'] = '获取失败';
        }
        return json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    /**
     * 分配权限
     * @param AssignPermissionsRequest $request
     * @return string
     */
    public function assignPermissions(AssignPermissionsRequest $request): string
    {
        $result = ['code' => 0, 'message' => '保存成功'];
        list($res, $message) = $this->model->assignPermissions($request);
        if (!$res) {
            $result['code'] = -1;
            $result['message'] = $message;
        }
        return json_encode($result, JSON_UNESCAPED_UNICODE);
    }
}
