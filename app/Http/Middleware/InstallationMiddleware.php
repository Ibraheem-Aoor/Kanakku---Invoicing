<?php

namespace Kanakku\Http\Middleware;

use Closure;
use Kanakku\Models\Setting;

class InstallationMiddleware
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
        if (!\Storage::disk('local')->has('database_created')) {
            return redirect('/installer');
        }

        if (\Storage::disk('local')->has('database_created')) {
            if (Setting::getSetting('profile_complete') !== 'COMPLETED') {
                return redirect('/installer');
            }
        }

        return $next($request);
    }
}
