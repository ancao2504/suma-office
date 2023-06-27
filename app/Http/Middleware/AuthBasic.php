<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Str;
use App\Helpers\Api\Response;

class AuthBasic
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
        if(Str::contains($request->header()['authorization'][0], 'Bearer')) {
            return $next($request);
        } else {
            if (($request->getUser() != config('constants.api.key.username')) || ($request->getPassword() != config('constants.api.key.password'))) {
                return Response::responseWarning("Invalid Credential");
            } else {
                return $next($request);
            }
        }
    }
}
