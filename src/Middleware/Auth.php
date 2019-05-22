<?php

namespace MmanagerPOS\Middleware;

class Auth
{
    protected $router;
    protected $auth;
    /**
     * Auth middleware invokable class
     *
     * @param  \Psr\Http\Message\ServerRequestInterface $request  PSR7 request
     * @param  \Psr\Http\Message\ResponseInterface      $response PSR7 response
     * @param  callable                                 $next     Next middleware
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __construct($router) {
        $this->router = $router;
        $this->is_authenticated = self::is_authenticated();
    }
    public function __invoke($request, $response, $next)
    {
        if ($this->is_authenticated) {
            $response = $next($request, $response);
        } else {
            $response = $response->withRedirect($this->router->pathFor('user-login'));
        }
        return $response;
    }
    public function is_authenticated() {
        return (isset($_SESSION['user_name'])) ? true : false;
    }
}