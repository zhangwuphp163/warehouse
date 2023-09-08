<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class BaseController extends Controller
{
    public function validateToken(){

    }
    public function menu(): array
    {
        return ['code' => 200 ,'msg' => 'success','data' => [
            [
                'id' => '/workspace',
                'icon' => 'layui-icon-home',
                'title' => '工作空间',
                'children' => [
                    [
                        'id' =>  "/workspace/dashboards",
                        'icon' => "layui-icon-util",
                        'title' => "Dashboards"
                    ]
                ]
            ],
            [
                'id' => '/list',
                'icon' => 'layui-icon-app',
                'title' => '操作',
                'children' => [
                    [
                        'id' =>  "/list/material",
                        'icon' => "layui-icon-list",
                        'title' => "物料列表"
                    ]
                ]
            ]
        ]];
    }

}
