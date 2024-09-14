<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AssignPermissionsRequest;
use App\Http\Requests\CreateRoleRequest;
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
    public function create(CreateRoleRequest $request): string
    {
        $result = ['code' => 0, 'message' => '保存成功'];
        list($res, $message) = $this->model->store($request);
        if (!$res) {
            $result['code'] = -1;
            $result['message'] = $message;
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
