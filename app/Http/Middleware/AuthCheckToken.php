<?php

namespace App\Http\Middleware;

use Closure;
use App\Helpers\Api\Response;
use Illuminate\Support\Facades\DB;

class AuthCheckToken
{
    public function handle($request, Closure $next)
    {
        $authorization = $request->header('Authorization');

        if(empty($authorization)){
            return Response::responseWarning('Empty Credential');
        }

        $token = explode(" ", $authorization);

        if(count($token) <> 2) {
            return Response::responseWarning('Invalid Authorization 2');
        }

        if(trim($token[0]) <> 'Bearer') {
            return Response::responseWarning('Invalid Authorization 2');
        }

        $access_token = trim($token[1]);
        $sql = DB::table('user_api_office')->lock('with (nolock)')
                ->selectRaw("isnull(user_api_office.office_token, '') as office_token,
                            isnull(user_api_office.office_expired, 0) as office_expired")
                ->where('user_api_office.office_token', $access_token)
                ->orderByRaw("isnull(user_api_office.id, 0) desc")
                ->first();

        if(empty($sql->office_token)) {
            return Response::responseWarning('Token not found');
        }

        if($sql->office_expired <= time()) {
            $expired_at = time() + 24 * 60 * 60;

            DB::transaction(function () use ($access_token, $expired_at) {
                DB::update('update user_api_office set office_expired=? where office_token=?',
                    [ $expired_at, $access_token ]);
            });
        }
        return $next($request);
    }
}
