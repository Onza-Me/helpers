<?php

namespace OnzaMe\Helpers\Http\Middleware;

use Closure;
use OnzaMe\JWT\Http\Middleware\JWTAuth;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class UnauthorizedJWTAuth extends JWTAuth
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
        try {
            return parent::handle($request, $next);
        } catch (UnauthorizedHttpException $e) {
            return $next($request);
        }
    }
}
