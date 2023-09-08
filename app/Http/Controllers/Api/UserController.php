<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Psy\Util\Str;

class UserController extends Controller
{
    public function index(Request $request){
        $pageInfo = $request->get('pageInfo');
        $builder = User::query();
        $total = $builder->count();
        $limit = $pageInfo['limit']??10;
        $current = $pageInfo['current'];
        $offset = ($current - 1) * $limit;
        $data = $builder->offset($offset)->orderBy('id','desc')->limit($limit)->get()->toArray();
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
        $createOrUpdateData = $request->only(['username','name']);
        try{
            if(!empty($params['id'])){
                $user = User::whereId($params['id'])->first();
                if (empty($user)) throw new \Exception("找不到用户信息");
                $exists = User::whereUsername($params['username'])->where('id','<>',$params['id'])->exists();
                if($exists) throw new \Exception("用户账号【{$params['username']}】已经被使用");
                if(!empty($params['password'])){
                    $createOrUpdateData['password'] = Hash::make($params['password']);
                }
                $user->update($createOrUpdateData);
            }else{
                $user = User::whereUsername($params['username'])->first();
                if($user) throw new \Exception("用户账号【{$params['barcode']}】已经被使用");
                if(empty($params['password'])){
                    $params['password'] = '123456';
                }
                $createOrUpdateData['password'] = Hash::make($params['password']);
                User::create($createOrUpdateData);
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
