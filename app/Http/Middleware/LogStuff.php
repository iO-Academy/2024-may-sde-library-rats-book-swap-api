<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class LogStuff
{
    public function handle(Request $request, Closure $next): Response
    {
        $data = ['path' => $request->getPathInfo(), 'method' => $request->getMethod()];
        $message = str_replace('/', '_', trim($request->getPathInfo(), '/'));
        Log::info($message, $data);
        return $next($request);
    }
}
