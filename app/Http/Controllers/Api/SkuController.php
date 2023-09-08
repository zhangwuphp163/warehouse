<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sku;
use App\Models\SkuItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SkuController extends Controller
{
    public function index(Request $request){
        $pageInfo = $request->get('pageInfo');
        $builder = Sku::query();
        $total = $builder->count();
        $limit = $pageInfo['limit']??10;
        $current = $pageInfo['current'];
        $offset = ($current - 1) * $limit;
        $data = Sku::query()->offset($offset)->limit($limit)->get()->toArray();
        return [
            'code' => 200,
            'msg' => 'success',
            'data' => $data,
            'total' => $total
        ];
    }

    public function createOrUpdate(Request $request){
        try{
            DB::beginTransaction();
            $params = $request->only(['id','barcode','name','description','items']);
            if(!empty($params['id'])){
                $msg = "编辑成功";
                $sku = Sku::whereId($params['id'])->first();
                if(empty($sku)) throw new \Exception("找不到商品信息");
                $sku->update([
                    'barcode' => $params['barcode'],
                    'name' => $params['name'],
                    'description' => $params['description']
                ]);
                $itemIds = array_filter(array_column($params['items'],'id'));
                $sku->items()->whereNotIn('id',$itemIds)->delete();
                foreach ($params['items'] as $item){
                    if(empty($item['id'])){
                        SkuItem::create([
                            'sku_id' => $sku->id,
                            'material_id' => $item['material_id'],
                            'qty' => $item['qty']
                        ]);
                    }else{
                        $sku->items()->whereId($item['id'])->update([
                            'sku_id' => $sku->id,
                            'material_id' => $item['material_id'],
                            'qty' => $item['qty']
                        ]);
                    }
                }
            }else{
                $msg = "创建成功";
                $exists = Sku::whereBarcode($params['barcode'])->exists();
                if($exists) throw new \Exception("条码【{$params['barcode']}】已经被使用");
                $sku = Sku::create([
                    'barcode' => $params['barcode'],
                    'name' => $params['name'],
                    'description' => $params['description']
                ]);
                foreach ($params['items'] as $key => $item){
                    SkuItem::create([
                        'sku_id' => $sku->id,
                        'material_id' => $item['material_id'],
                        'qty' => $item['qty']
                    ]);
                }
            }

            DB::commit();;
            return [
                'code' => 200,
                'msg' => $msg
            ];
        }catch (\Exception $exception){
            DB::rollBack();
            return [
                'code' => 400,
                'msg' => $exception->getMessage()
            ];
        }
    }

    public function delete($id){
        try {
            $sku = Sku::whereId($id)->first();
            if(empty($sku)) throw new \Exception("找不到商品信息");
            $sku->delete();
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

    public function getInfo($id){
        $sku = Sku::with('items')->whereId($id)->first();
        return [
            'code' => 200,
            'data' => $sku->toArray(),
            'msg' => 'success'
        ];
    }
}
