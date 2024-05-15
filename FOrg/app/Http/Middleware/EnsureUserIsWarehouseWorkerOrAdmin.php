<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsWarehouseWorkerOrAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(!$request->user() ||
            (!$request->user()->hasRole('admin') &&
            !$request->user()->hasRole('warehouse_worker'))){
            return response()->json(['message' => 'Missing role.'], 403);

        }
        return $next($request);
    }
}
