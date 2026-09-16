<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Visitor;
use Symfony\Component\HttpFoundation\Response;

class VisitorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

if ($request->is('livewire/*')) {
        return $next($request);
    }

    $ip = $request->ip();

        $exists = Visitor::where('ip_address', $ip)
            ->whereDate('created_at', today())
            ->exists();

        if (!$exists) {

           
        $signature = md5(
            $ip .
            $request->userAgent() .
            date('Y-m-d')
        );

        Visitor::firstOrCreate(
            ['signature' => $signature],
            [
                'ip_address' => $ip,
                'browser' => $request->userAgent(),
                'platform' => php_uname('s'),
                'url' => $request->fullUrl(),
            ]
        );

        }
/* 
     $ip = $request->ip();

        $signature = md5(
            $ip .
            $request->userAgent() .
            date('Y-m-d')
        );

        Visitor::firstOrCreate(
            ['signature' => $signature],
            [
                'ip_address' => $ip,
                'browser' => $request->userAgent(),
                'platform' => php_uname('s'),
                'url' => $request->fullUrl(),
            ]
        );
 */        return $next($request);
    }
}
