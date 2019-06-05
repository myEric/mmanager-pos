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
    public function __construct($router, $domain=null) {
        $this->router = $router;
        $this->is_authenticated = self::is_authenticated($domain);
    }
    public function __invoke($request, $response, $next)
    {
        if ($this->is_authenticated) {
            $response = $next($request, $response);
        } else {
            $response = $response->withRedirect($this->router->pathFor('client-login'));
        }
        return $response;
    }
    /**
     * Check is user is logged in and determines user domain
     * @param type $domain 
     * @return type bool
     */
    public function is_authenticated($domain) {
        return (isset($_SESSION['user_name']) && $_SESSION['user_domain'] == $domain) ? true : false;
    }
}