<?php

namespace App\Http\Middleware;

use App\Libraries\CustomLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\GroupHandler;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Symfony\Component\HttpFoundation\Response;

class LogRequest
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        return $next($request);
    }

    public function terminate(Request $request, Response $response)
    {
        CustomLog::info('api-log',sprintf("Request from %s\n", $request->getClientIp()) . $this->request($request) . "\n\nResponse:\n" . $this->response($request, $response));
    }

    public function request(Request $request) {
        $data = sprintf(
            "%s %s %s\n",
            $request->method(),
            $request->url() . (!empty($request->getQueryString()) ? '?' . $request->getQueryString() : ''),
            $request->getProtocolVersion()
        );
        foreach ($this->getHeaderList($request->header()) as $name => $value) {
            $data .= $name . ': ' . $value . "\n";
        }
        return $data . "\r\n" . file_get_contents('php://input');
    }

    private function getHeaderList($headers) {
        $headerList = [];
        foreach ($headers as $name => $value) {
            $name = ucwords(strtolower($name));
            $headerList[$name] = $value[0];
        }
        return $headerList;
    }

    private function response(Request $request, Response $response) {
        return $response->__toString();
    }
}
