<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Stevebauman\Location\Facades\Location;
use Symfony\Component\HttpFoundation\Response;

class CheckIp
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if($request->header('X-Forwarded-For') == env('IP_COMPANY')){
            return $next($request);
        } else {
            return response()->json(["message"=>  'You must be checkin and checkout by IP Company']);
        }
    }
}
