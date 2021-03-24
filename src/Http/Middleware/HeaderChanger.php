<?php

namespace OnzaMe\Helpers\Http\Middleware;

use Closure;

class HeaderChanger
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
        $response = $next($request);

        $change = config('onzame_helpers.headers.change');
        $remove = config('onzame_helpers.headers.remove');

        foreach($change as $header => $value) {
            $response->headers->set($header, $value);
        }
        foreach($remove as $header) {
            $response->headers->remove($header);
        }

        return $response;
    }
}
