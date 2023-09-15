<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Libraries\ApiResponse;
use App\Models\Client;
use App\Models\Material;
use App\Models\Sku;
use App\Models\Supplier;
use App\Models\Warehouse;
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
                case 'client':
                    $rows = Client::query()->get(['id','company_name','code']);
                    foreach ($rows as $row){
                        $data[] = [
                            'value' => $row->id,
                            'label' => $row->company_name."({$row->code})",
                        ];
                    }
                    break;
                case 'warehouse':
                    $rows = Warehouse::query()->get();
                    foreach ($rows as $row){
                        $data[] = [
                            'value' => $row->id,
                            'label' => $row->name."({$row->code})",
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

    public function getTableHeaders(Request $request,String $type)
    {
        try{
            $data = [];
            if($type  == 'client'){
                $fillable = ['code', 'company_name', 'shipper_name', 'shipper_company', 'shipper_phone', 'shipper_address', 'shipper_country', 'shipper_province', 'shipper_city', 'shipper_postal_code', 'shipper_email', 'shipper_tax_number_type', 'shipper_tax_number', 'shipper_id_card_number_type', 'shipper_id_card_number', 'ioss_number', 'ioss_issuer_country_code'];
                foreach ($fillable as $fill){
                    $data[] = [
                        'key' => $fill,
                        'width' => "180px",
                        'title' => $fill,
                        'align' => 'center'
                    ];
                }
                $data[] = [
                    'key' => 'operation',
                    'width' => '120px',
                    'title' => 'operation',
                    'align' => 'center',
                    'customSlot' => 'operator',
                    'fixed' => 'right'
                ];
            }

            return [
                'code' => 200,
                'msg' => 'success',
                'data' => $data,
            ];
        }catch (\Exception $exception){
            return ApiResponse::error($exception->getMessage());
        }
    }
}
