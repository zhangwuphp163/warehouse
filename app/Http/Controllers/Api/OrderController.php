<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    function index(Request $request){
        $pageInfo = $request->get('pageInfo');
        $builder = Order::query();
        $total = $builder->count();
        $limit = $pageInfo['limit']??10;
        $current = $pageInfo['current'];
        $offset = ($current - 1) * $limit;
        $data = $builder->offset($offset)->limit($limit)->get()->toArray();
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
            $params = $request->only(['id','order_number','remarks','items']);
            if(!empty($params['id'])){
                $msg = "更新成功";
                $order = Order::whereId($params['id'])->first();
                if(empty($order)) throw new \Exception("找不到订单");
                $order->update([
                    'order_number' => $params['order_number'],
                    'remarks' => $params['remarks'],
                ]);
                $itemIds = array_filter(array_column($params['items'],'id'));
                $order->items()->whereNotIn('id',$itemIds)->delete();
                foreach ($params['items'] as $item){
                    if(empty($item['id'])){
                        OrderItem::create([
                            'order_id' => $order->id,
                            'sku_id' => $item['sku_id'],
                            'plan_qty' => $item['plan_qty'],
                            'plan_unit_price' => $item['plan_unit_price'],

                        ]);
                    }else{
                        $order->items()->whereId($item['id'])->update([
                            'order_id' => $order->id,
                            'sku_id' => $item['sku_id'],
                            'plan_qty' => $item['plan_qty'],
                            'plan_unit_price' => $item['plan_unit_price'],
                        ]);
                    }
                }
            }else{
                $msg = "创建成功";
                $exists = Order::where('order_number',$params['order_number'])->exists();
                if($exists) throw new \Exception("订单号【{$params['order_number']}】已经被使用");
                $order = Order::create([
                    'order_number' => $params['order_number'],
                    'status' => 'pending',
                    'remarks' => $params['remarks']
                ]);
                foreach ($params['items'] as $key => $item){
                    OrderItem::create([
                        'order_id' => $order->id,
                        'sku_id' => $item['sku_id'],
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
            $asn = Order::whereId($id)->first();
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
        $order = Order::with('items')->whereId($id)->first();
        return [
            'code' => 200,
            'data' => $order->toArray(),
            'msg' => 'success'
        ];
    }
}
