<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Request;

class LogRouteMiddleware
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
        $response = $next($request);

        if (defined('LARAVEL_START') and $response instanceof Response) {
            $response->headers->add(['X-RESPONSE-TIME' => microtime(true) - LARAVEL_START]);
        }

        return $response;
    }

    public function terminate($request, $response)
    {
        $method = strtoupper($request->getMethod());
        $uri = $request->getRequestUri();
        $status = $response->getStatusCode();
        $time =  microtime(true) - LARAVEL_START;
        $time = round($time * 1000);
        $log_message = "{$method}\t\t{$uri}\t\t{$status}\t\t{$time}ms";

        $file = 'app-info.log';
        file_put_contents($file, $log_message . "\n", FILE_APPEND);
    }
}
