<?php

namespace VoyagerBlocker\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Schema;
use VoyagerBlocker\Models\VoyagerBlocker;

class VoyagerBlockerMiddleware
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
        if (Schema::hasTable('voyager_blocker')) {
            $clientIp    = $request->getClientIp();

            $blocker = VoyagerBlocker::first();

            if (!is_null($blocker->ips)) {
                $ips = json_decode($blocker->ips, true);

                if (!empty(array_filter($ips['whitelist']))) {
                    if (in_array($clientIp, $ips['whitelist'])) {
                        return $next($request);
                    }

                    return abort(404);
                }

            }

        }
        return $next($request);
    }
}