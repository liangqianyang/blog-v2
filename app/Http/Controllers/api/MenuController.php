<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public Menu $model;

    public function __construct()
    {
        $this->model = new Menu();
    }

    /**
     * 菜单列表
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
     * 获取导航菜单
     * @param Request $request
     * @return string
     */
    public function navbarMenu(Request $request): string
    {
        $user = $request->user();
        $data = $this->model->getNavbarMenus($user->id);
        return json_encode(['code' => 0, 'message' => '', 'data' => $data], JSON_UNESCAPED_UNICODE);
    }
}
