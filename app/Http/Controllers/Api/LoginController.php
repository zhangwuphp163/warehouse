<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class LoginController extends Controller
{

    public function post(Request $request){
        $user = $request->get('username','');
        $password = $request->get('password','');
        if(Auth::attempt(['username' => $user,'password' => $password])){
            /** @var User $admin */
            $admin = Auth::user();
            if($admin->validity_at <= Carbon::now()){
                $token = Str::random(60);
                $expires_in = 86400*365;
                $admin->api_token = $token;
                $admin->validity_at = date('Y-m-d H:i:s',time()+$expires_in);
                $admin->save();
            }
            return [
                'code' => 200,
                'msg' => 'success',
                'data' =>
                    [
                        'userId'=> $admin->id,
                        'token'=> $admin->api_token
                    ]
            ];
        }else{
            return [
                'code' => 403,
                'msg' => '账号或密码错误',
                'data' => []
            ];
        }
    }
}
