<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use Cache;
use Carbon\Carbon;

use App\User;

class LastUserActivity
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
        if(Auth::check()){
            $expireAt = Carbon::now()->addMinutes(2);

            //menyimpan cache kalau user online di server
            Cache::put('user-is-online-'.Auth::user()->id, true, $expireAt);
            User::find(Auth::user()->id)->update(['last_online'=>Carbon::now()->timestamp]);
        }
        return $next($request);
    }
}
