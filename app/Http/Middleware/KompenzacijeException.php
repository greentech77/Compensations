<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;

class KompenzacijeException
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (session()->exists('exception')) {
            $e = session()->pull('exception');
            Inertia::share('exception', [
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ]);

            // exception metadata
            $meta = $e->getMeta();
            if ($meta !== null) {

                if (isset($meta['modal']))
                session()->flash('modal', $meta['modal']);
            }
        }

        return $next($request);
    }
}
