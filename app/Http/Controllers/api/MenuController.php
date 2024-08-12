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

    public function list(Request $request): false|string
    {
        $params = $request->query();
        $data = $this->model->result($params);
        return json_encode(['code'=>0,'message'=>'','data'=>$data],JSON_UNESCAPED_UNICODE);
    }
}
