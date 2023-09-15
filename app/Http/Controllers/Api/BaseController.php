<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Libraries\ApiResponse;
use App\Models\Box;
use App\Models\Client;
use App\Models\Country;
use App\Models\Material;
use App\Models\Warehouse;
use Dflydev\DotAccessData\Data;
use Illuminate\Http\Request;

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

    public function setting(Request $request): \Illuminate\Http\JsonResponse|array
    {
        try{
            $pageInfo = $request->get('pageInfo');
            $limit = $pageInfo['limit']??10;
            $current = $pageInfo['current'];
            $offset = ($current - 1) * $limit;
            $type = $request->get('type','');
            $builder = match ($type) {
                'country' => Country::query(),
                'warehouse' => Warehouse::query(),
                'box' => Box::query()->with('warehouse'),
                'client' => Client::query(),
                default => throw new \Exception("无效的类型"),
            };
            $total = $builder->count();
            $data = $builder->orderBy('id', 'desc')->offset($offset)->limit($limit)->get()->toArray();
            if($type  == 'box'){
                foreach ($data as &$row){
                    $row['is_active'] = (bool)$row['is_active'];
                    $row['warehouse_name'] = $row['warehouse']['name'];
                }
            }


            return [
                'code' => 200,
                'msg' => 'success',
                'data' => $data,
                'total' => $total
            ];
        }catch (\Exception $exception){
            return ApiResponse::error($exception->getMessage());
        }
    }

    public function createOrUpdate(Request $request,String $type): array
    {
        $params = $request->all();
        try{
            switch ($type){
                case "warehouse":
                    $createOrUpdateData = [
                        'code' => $params['code'],
                        'name' => $params['name'],
                    ];
                    if(!empty($params['id'])){
                        $warehouse = Warehouse::whereId($params['id'])->first();
                        if (empty($warehouse)) throw new \Exception("找不到仓库信息");
                        $exists = Warehouse::whereCode($params['code'])->where('id','<>',$params['id'])->exists();
                        if($exists) throw new \Exception("仓库代码【{$params['code']}】已经被使用");
                        $warehouse->update($createOrUpdateData);
                    }else{
                        $warehouse = Warehouse::whereCode($params['code'])->first();
                        if($warehouse) throw new \Exception("仓库代码【{$params['barcode']}】已经被使用");
                        Warehouse::create($createOrUpdateData);
                    }
                    break;

                case "box":
                    $createOrUpdateData = [
                        'code' => $params['code'],
                        'name' => $params['name'],
                        'warehouse_id' => $params['warehouse_id'],
                        'client_id' => $params['client_id'],
                        'length' => $params['length'],
                        'width' => $params['width'],
                        'height' => $params['height'],
                        'weight' => $params['weight'],
                        'is_active' => $params['is_active'],
                    ];
                    if(!empty($params['id'])){
                        $box = Box::whereId($params['id'])->first();
                        if (empty($box)) throw new \Exception("找不到箱子信息");
                        $exists = Box::whereCode($params['code'])->where('id','<>',$params['id'])->exists();
                        if($exists) throw new \Exception("箱子代码【{$params['code']}】已经被使用");
                        $box->update($createOrUpdateData);
                    }else{
                        $box = Box::whereCode($params['code'])->first();
                        if($box) throw new \Exception("箱子代码【{$params['code']}】已经被使用");
                        Box::create($createOrUpdateData);
                    }
                    break;
                case "client":
                    $createOrUpdateData = [
                        "code" => $params["code"],
                        "company_name" => $params["company_name"],
                        "shipper_name" => $params["shipper_name"],
                        "shipper_company" => $params["shipper_company"],
                        "shipper_phone" => $params["shipper_phone"],
                        "shipper_address" => $params["shipper_address"],
                        "shipper_country" => $params["shipper_country"],
                        "shipper_province" => $params["shipper_province"],
                        "shipper_city" => $params["shipper_city"],
                        "shipper_postal_code" => $params["shipper_postal_code"],
                        "shipper_email" => $params["shipper_email"],
                        "shipper_tax_number_type" => $params["shipper_tax_number_type"],
                        "shipper_tax_number" => $params["shipper_tax_number"],
                        "shipper_id_card_number_type" => $params["shipper_id_card_number_type"],
                        "shipper_id_card_number" => $params["shipper_id_card_number"],
                        "ioss_number" => $params["ioss_number"],
                        "ioss_issuer_country_code" => $params["ioss_issuer_country_code"]
                    ];
                    if(!empty($params['id'])){
                        $client = Client::whereId($params['id'])->first();
                        if (empty($client)) throw new \Exception("找不到客户信息");
                        $exists = Client::whereCode($params['code'])->where('id','<>',$params['id'])->exists();
                        if($exists) throw new \Exception("客户代码【{$params['code']}】已经被使用");
                        $client->update($createOrUpdateData);
                    }else{
                        $client = Client::whereCode($params['code'])->first();
                        if($client) throw new \Exception("客户代码【{$params['barcode']}】已经被使用");
                        Client::create($createOrUpdateData);
                    }
                    break;
                default:
                    throw new \Exception("无效的类型");
            }

            return [
                'code' => 200,
                'msg' => $params['id']?'更新成功':'创建成功'
            ];
        }catch (\Exception $exception){
            return [
                'code' => 400,
                'msg' => $exception->getMessage()
            ];
        }
    }

}
