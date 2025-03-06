<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangeMenuStateRequest;
use App\Http\Requests\CreateMenuRequest;
use App\Http\Requests\UpdateMenuRequest;
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
     * 获取菜单选择列表
     * @param Request $request
     * @return string
     */
    public function menuSelectList(Request $request):string
    {
        $data = $this->model->getMenuSelectList();
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

    /**
     * 添加菜单
     * @param CreateMenuRequest $request
     * @return string
     */
    public function store(CreateMenuRequest $request):string
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
     * 更新菜单
     * @param UpdateMenuRequest $request
     * @return string
     */
    public function update(UpdateMenuRequest $request): string
    {
        $result = ['code' => 0, 'message' => '保存成功'];
        list($res, $message) = $this->model->updateDate($request);
        if (!$res) {
            $result['code'] = -1;
            $result['message'] = $message;
        }
        return json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    /**
     * 更改菜单状态
     * @param ChangeMenuStateRequest $request
     * @return string
     */
    public function changeMenuState(ChangeMenuStateRequest $request): string
    {
        $result = ['code' => 0, 'message' => '操作成功'];
        list($res, $message) = $this->model->changeState($request);
        if (!$res) {
            $result['code'] = -1;
            $result['message'] = $message;
        }
        return json_encode($result, JSON_UNESCAPED_UNICODE);
    }
}
