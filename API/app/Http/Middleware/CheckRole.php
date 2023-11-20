<?php

namespace App\Http\Middleware;

use App\Common\Role;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->user()->role === Role::ADMIN || auth()->user()->id == $request->id) {
            return $next($request);
        } else {
            return response()->json(["message"=>  Response::HTTP_FORBIDDEN]);
        }
    }
}
