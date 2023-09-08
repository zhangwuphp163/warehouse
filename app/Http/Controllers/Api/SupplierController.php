<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Libraries\ApiResponse;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    function index()
    {
        $data = Supplier::query()->get()->toArray();
        return [
            'code' => 200,
            'msg' => 'success',
            'data' => $data
        ];
    }

    function createOrUpdate(Request $request)
    {
        try{
            $params = $request->only(['id','name']);
            $id = $params['id'];
            $name = trim($params['name']);
            if(!empty($params['id'])){
                $supplier = Supplier::whereId($id)->first();
                if(empty($supplier)) throw new \Exception("找不到供应商");
                $exists = Supplier::where('name',$name)->where('id','<>',$id)->exists();
                if($exists) throw new \Exception("供应商名称已经被使用，请换一个！");
                $supplier->update(['name' => $name]);
            }else{
                $exists = Supplier::where('name',$name)->exists();
                if($exists) throw new \Exception("供应商名称已经被使用，请换一个！");
                Supplier::create(['name' => $name]);
            }
            return ApiResponse::success([],'创建成功');
        }catch (\Exception $e){
            return ApiResponse::error($e->getMessage());
        }
    }

    public function delete($id){
        try {
            $supplier = Supplier::whereId($id)->first();
            if(empty($supplier)) throw new \Exception("找不到供应商");
            $supplier->delete();
            return [
                'code' => 200,
                'msg' => '删除成功'
            ];
        }catch (\Exception $exception){
            return [
                'code' => 400,
                'msg' => $exception->getMessage()
            ];
        }
    }
}
