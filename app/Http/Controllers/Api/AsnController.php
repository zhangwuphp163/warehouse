<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Libraries\ApiResponse;
use App\Models\Asn;
use App\Models\AsnItem;
use App\Models\Material;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AsnController extends Controller
{
    function index(Request $request){
        $pageInfo = $request->get('pageInfo');
        $builder = Asn::query();
        $total = $builder->count();
        $limit = $pageInfo['limit']??10;
        $current = $pageInfo['current'];
        $offset = ($current - 1) * $limit;
        $rows = Asn::query()->with('items')->offset($offset)->limit($limit)->get()->toArray();
        foreach ($rows as &$row){
            $totalPlanQty = array_sum(array_column($row['items'],'plan_qty'));
            $totalActualQty = array_sum(array_column($row['items'],'actual_qty'));
            $row['processing'] = ceil($totalActualQty/$totalPlanQty);
        }
        return [
            'code' => 200,
            'msg' => 'success',
            'data' => $rows,
            'total' => $total
        ];
    }

    public function createOrUpdate(Request $request){
        try{
            DB::beginTransaction();
            $params = $request->only(['id','asn_number','remarks','items']);
            if(!empty($params['id'])){
                $msg = "更新成功";
                $asn = Asn::whereId($params['id'])->first();
                if(empty($asn)) throw new \Exception("找不到预报单信息");
                $asn->update([
                    'asn_number' => $params['asn_number'],
                    'remarks' => $params['remarks'],
                ]);
                $itemIds = array_filter(array_column($params['items'],'id'));
                $asn->items()->whereNotIn('id',$itemIds)->delete();
                foreach ($params['items'] as $item){
                    if(empty($item['id'])){
                        AsnItem::create([
                            'asn_id' => $asn->id,
                            'material_id' => $item['material_id'],
                            'supplier_id' => $item['supplier_id'],
                            'plan_qty' => $item['plan_qty'],
                            'plan_unit_price' => $item['plan_unit_price'],

                        ]);
                    }else{
                        $asn->items()->whereId($item['id'])->update([
                            'asn_id' => $asn->id,
                            'material_id' => $item['material_id'],
                            'supplier_id' => $item['supplier_id'],
                            'plan_qty' => $item['plan_qty'],
                            'plan_unit_price' => $item['plan_unit_price'],
                        ]);
                    }
                }
            }else{
                $msg = "创建成功";
                $exists = Asn::where('asn_number',$params['asn_number'])->exists();
                if($exists) throw new \Exception("预报单号码【{$params['asn_number']}】已经被使用");
                $asn = Asn::create([
                    'asn_number' => $params['asn_number'],
                    'status' => 'pending',
                    'remarks' => $params['remarks']
                ]);
                foreach ($params['items'] as $key => $item){
                    AsnItem::create([
                        'asn_id' => $asn->id,
                        'material_id' => $item['material_id'],
                        'supplier_id' => $item['supplier_id'],
                        'plan_qty' => $item['plan_qty'],
                        'plan_unit_price' => $item['plan_unit_price'],
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
            $asn = Asn::whereId($id)->first();
            if(empty($asn)) throw new \Exception("找不到预报单信息");
            $asn->delete();
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
        $asn = Asn::with('items')->whereId($id)->first();
        return [
            'code' => 200,
            'data' => $asn->toArray(),
            'msg' => 'success'
        ];
    }

    public function getItems($asn_number){
        try{
            $asn = Asn::where('asn_number',$asn_number)->first();
            if(empty($asn)) throw new \Exception("找不到入库单【{$asn_number}】");
            $items = $asn->items()->with('material')->with('supplier')->orderBy('inbound_at','desc')->get();
            $data = [];
            foreach ($items as $item){
                $data[] = [
                    'supplier' => $item->supplier->name,
                    'material_name' => $item->material->name,
                    'material_barcode' => $item->material->barcode,
                    'plan_qty' => $item->plan_qty,
                    'actual_qty' => $item->actual_qty
                ];
            }
            return [
                'code' => 200,
                'data' => $data,
                'msg' => 'success'
            ];
        }catch (\Exception $exception){
            return ApiResponse::error($exception->getMessage());
        }
    }

    public function inbound(Request $request){
        try{
            DB::beginTransaction();;
            $asn_number = $request->get("asn_number",'');
            $material_barcode = $request->get("material_barcode",'');
            $qty = $request->get("qty",'');
            $asn = Asn::whereAsnNumber($asn_number)->first();
            if(empty($asn)) throw new \Exception("找不到入库单【{$asn_number}】");
            $material = Material::whereBarcode($material_barcode)->first();
            if(empty($material)) throw new \Exception("找不到物料条码【{$material_barcode}】");
            $items = AsnItem::where('asn_id',$asn->id)->where('material_id',$material->id)->get();
            if(sizeof($items) == 0) throw new \Exception("入库单【{$asn_number}】找不到物料信息");
            foreach ($items as $item){
                if($item->plan_qty <= $item->actual_qty) continue;
                $diff_qty = $item->plan_qty - $item->actual_qty;
                $min_qty = min($qty,$diff_qty);
                $item->actual_qty += $min_qty;
                $qty -= $min_qty;
                $item->inbound_at = Carbon::now();
                $item->save();
            }
            if($qty > 0){
                $item = $items->first();
                $item->actual_qty += $qty;
                $item->inbound_at = Carbon::now();
                $item->save();
            }
            $asn->status = "receiving";
            $asn->save();
            DB::commit();
            return $this->getItems($asn_number);
        }catch (\Exception $exception){
            DB::rollBack();
            return ApiResponse::error($exception->getMessage());
        }
    }
}
