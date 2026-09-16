<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckDomain
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        
        //verification si le site est en production pour la restriction des domaines autres.
        
        if ( config("app.env") == "production" ) {
            $allowedDomain = 'swootbio.com';
            $requestDomain = $request->getHttpHost();

            if ($requestDomain !== $allowedDomain) {
                return redirect()->route('pageNotAllowed'); // Rediriger vers une page d'erreur
            }
        }

        return $next($request);
    }
}
