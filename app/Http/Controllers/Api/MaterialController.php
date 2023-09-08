<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Material;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    public function index(Request $request){
        $pageInfo = $request->get('pageInfo');
        $builder = Material::query();
        $total = $builder->count();
        $limit = $pageInfo['limit']??10;
        $current = $pageInfo['current'];
        $offset = ($current - 1) * $limit;
        $data = Material::query()->offset($offset)->orderBy('id','desc')->limit($limit)->get()->toArray();
        return [
            'code' => 200,
            'msg' => 'success',
            'data' => $data,
            'total' => $total
        ];
    }

    /**
     * @throws \Exception
     */
    public function createOrUpdate(Request $request){
        $params = $request->all();
        $createOrUpdateData = $request->only(['barcode','name','description','unit_price']);
        try{
            if(!empty($params['id'])){
                $material = Material::whereId($params['id'])->first();
                if (empty($material)) throw new \Exception("找不到物料信息");
                $exists = Material::whereBarcode($params['barcode'])->where('id','<>',$params['id'])->exists();
                if($exists) throw new \Exception("条码【{$params['barcode']}】已经被使用");
                $material->update($createOrUpdateData);
            }else{
                $material = Material::whereBarcode($params['barcode'])->first();
                if($material) throw new \Exception("条码【{$params['barcode']}】已经被使用");
                Material::create($createOrUpdateData);
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

    public function delete($id){
        try {
            $material = Material::whereId($id)->first();
            if(empty($material)) throw new \Exception("找不到物料信息");
            $material->delete();
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
