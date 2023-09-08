<?php

namespace App\Http\Middleware;

use App\Libraries\CustomLog;
use Illuminate\Http\Request;

class VerifyApiToken
{
    public function handle(Request $request): void
    {
        CustomLog::info('debug',json_encode($request->all()));
    }
}
