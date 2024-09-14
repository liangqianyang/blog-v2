<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreatePermissionRequest;
use App\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public Permission $model;

    public function __construct()
    {
        $this->model = new Permission();
    }

    /**
     * 权限列表
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
     * 创建权限
     * @param CreatePermissionRequest $request
     * @return string
     */
    public function create(CreatePermissionRequest $request): string
    {
        $result = ['code' => 0, 'message' => '保存成功'];
        try {
            list($res, $message) = $this->model->store($request);
            if (!$res) {
                $result['code'] = -1;
                $result['message'] = $message;
            }
        } catch (\Exception $e) {
            $result['code'] = -1;
            $result['message'] = $e->getMessage();
        }
        return json_encode($result, JSON_UNESCAPED_UNICODE);
    }
}
