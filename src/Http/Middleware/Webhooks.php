<?php

namespace OnzaMe\Helpers\Http\Middleware;

use Closure;
use OnzaMe\JWT\Http\Middleware\JWTAuth;
use OnzaMe\Helpers\Exceptions\UnproccessableHttpRequestException;

class Webhooks extends JWTAuth
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
        $result = parent::handle($request, $next);

        if (auth()->user()->role !== 'server') {
            throw new UnproccessableHttpRequestException('', '', [], 403);
        }

        return $result;
    }
}
