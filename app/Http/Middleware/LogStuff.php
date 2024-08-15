<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use PHPUnit\Event\Event;
use Symfony\Component\HttpFoundation\Response;

class LogStuff
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        $data = [
            'path' => $request->getPathInfo(),
            'method' => $request->getMethod(),
            'status' => $response->getStatusCode()];
        $message = str_replace('/', '_', trim($request->getPathInfo(), '/'));
        if ($response->getStatusCode() == 500) {
            $message = '500_error_' . str_replace('/', '_', trim($request->getPathInfo(), '/'));
            Log::error($message, $data);
        }
        else{
            Log::info($message, $data);
        }
        return $response;
    }
}
