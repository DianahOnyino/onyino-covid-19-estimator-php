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

        // Add response time as an HTTP header. For better accuracy ensure this middleware
        // is added at the end of the list of global middlewares in the Kernel.php file
        if (defined('LARAVEL_START') and $response instanceof Response) {
            $response->headers->add(['X-RESPONSE-TIME' => microtime(true) - LARAVEL_START]);
        }

        return $response;
    }

    /**
     * Perform any final actions for the request lifecycle.
     *
     * @param  \Symfony\Component\HttpFoundation\Request $request
     * @param  \Symfony\Component\HttpFoundation\Response $response
     * @return void
     */
    public function terminate($request, $response)
    {
        // At this point the response has already been sent to the browser so any
        // modification to the response (such adding HTTP headers) will have no effect
        // if (defined('LARAVEL_START') and $request instanceof Request) {
            // Log::info('Response time', [
            //     'method' => $request->getMethod(),
            //     'uri' => $request->getRequestUri(),
            //     'statusCode' => $response->getStatusCode(),
            //     'seconds' => microtime(true) - LARAVEL_START,
            // ]);
        // }

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
