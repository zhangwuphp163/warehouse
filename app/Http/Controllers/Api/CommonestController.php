<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Libraries\ApiResponse;
use App\Models\Material;
use App\Models\Sku;
use App\Models\Supplier;
use Illuminate\Http\Request;

class CommonestController extends Controller
{
    public function getSelectList(Request $request): \Illuminate\Http\JsonResponse
    {
        try{
            $type = $request->get('type','material');
            $data = [];
            //$keyword = $request->get('keyword','');
            switch ($type){
                case 'material':
                    $rows = Material::query()->get(['id','name','barcode']);
                    foreach ($rows as $row){
                        $data[] = [
                            'value' => $row->id,
                            'label' => $row->name."($row->barcode)",
                        ];
                    }
                    break;
                case 'sku':
                    $rows = Sku::query()->get(['id','name','barcode']);
                    foreach ($rows as $row){
                        $data[] = [
                            'value' => $row->id,
                            'label' => $row->name."($row->barcode)",
                        ];
                    }
                    break;
                case 'supplier':
                    $rows = Supplier::query()->get(['id','name']);
                    foreach ($rows as $row){
                        $data[] = [
                            'value' => $row->id,
                            'label' => $row->name,
                        ];
                    }
                    break;
                default:
                    return ApiResponse::error("未知属性");
            }
            return ApiResponse::success($data,'success');
        }catch (\Exception $exception){
            return ApiResponse::error($exception->getMessage());
        }
    }
}
