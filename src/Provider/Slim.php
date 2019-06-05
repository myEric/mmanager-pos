<?php

declare(strict_types=1);

namespace MmanagerPOS\Provider;

use MmanagerPOS\Domain\User\CreateUser;
use MmanagerPOS\Domain\User\ListUsers;
use Pimple\ServiceProviderInterface;
use Doctrine\ORM\EntityManager;
use Pimple\Container;
use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;
use MmanagerPOS\Middleware\Auth;
use MmanagerPOS\Middleware\M_Auth;

/**
 * A ServiceProvider for registering services related
 * to Slim such as request handlers, routing and the
 * App service itself.
 */
class Slim implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(Container $cnt)
    {
        $cnt[ListUsers::class] = function (Container $cnt): ListUsers {
            return new ListUsers($cnt[EntityManager::class]);
        };

        $cnt[CreateUser::class] = function (Container $cnt): CreateUser {
            return new CreateUser(
                $cnt[EntityManager::class],
                Faker\Factory::create()
            );
        };

        $cnt[App::class] = function (Container $cnt): App {

            $app = new App($cnt);
            $app->add(new \Slim\Middleware\Session([
              'name' => 'mmanager_pos_session',
              'autorefresh' => true,
              'lifetime' => '1 day'
            ]));
            // Start the session
            session_start();

            // Routes
            $app->group('/auth', function (App $app) {
                $app->get('/client', function ($request, $response, $args) {
                    $data = [
                        'current_portal' => [
                            'name'         => __('Client Portal'),
                            'form_action'  => '/client/auth',
                        ],
                        'switch_to_other_portal' => [
                            'name' => __('Provider Portal'), 
                            'route' => '/auth/user',
                            'message' => __('Are You a Provider ?')
                        ]
                    ];
                    $this->view->addData($data);
                    return $this->view->render('auth::login');
                })->setName('client-login');

                $app->get('/user', function ($request, $response, $args) {
                    $data = [
                        'current_portal' => [
                            'name'          => __('Staff Portal'),
                            'form_action'  => '/user/auth',
                        ],
                        'switch_to_other_portal' => [
                            'name' => __('Client Portal'), 
                            'route' => '/auth/client',
                            'message' => __('Are You a Provider ?')
                        ]
                    ];

                    $this->view->addData($data);
                    return $this->view->render('auth::login');
                })->setName('user-login');

                $app->get('/provider', function ($request, $response, $args) {
                    $data = [
                        'current_portal' => [
                            'name'          => __('Staff Portal'),
                            'form_action'  => '/provider/auth',
                        ],
                        'switch_to_other_portal' => [
                            'name' => __('Client Portal'), 
                            'route' => '/auth/client',
                            'message' => __('Are You a Provider ?')
                        ]
                    ];

                    $this->view->addData($data);
                    return $this->view->render('auth::login');
                })->setName('user-login');
            });

            // $app->get('/user/login', function (Request $request, Response $response, array $args) use ($cnt) {
            //     $session = new M_Auth($cnt);
            //     if ( ! $session->isUserLoggedIn()) {
            //         return $this->view->render('auth::login');
            //     } else {
            //         return $response->withRedirect('/');
            //     }
            // })->setName('user-login');

            //Handle authentication with post data
            $app->get('/user/auth', function (Request $request, Response $response, array $args) use ($cnt) {
                $params = $request->getQueryParams();
                if ($params) {
                    $username = $params['username'];
                    $password = $params['password'];

                    $session = new M_Auth($cnt, 'user', 'login', $username, $password);

                    if ($session->isUserLoggedIn()) {
                        return $response->withJson(array('success' => true, 'redirect' => '/'), 200);
                    } else {
                        return $response->withJson(array('success' => false));
                    }
                } else {
                    return $response->withJson(array('success' => false));
                }
            })->setName('user-auth');

            $app->get('/client/auth', function (Request $request, Response $response, array $args) use ($cnt) {
                $params = $request->getQueryParams();
                if ($params) {
                    $username = $params['username'];
                    $password = $params['password'];

                    $session = new M_Auth($cnt, 'client', 'login', $username, $password);

                    if ($session->isUserLoggedIn()) {
                        return $response->withJson(array('success' => true, 'redirect' => '/client/1/home'), 200);
                    } else {
                        return $response->withJson(array('success' => false));
                    }
                } else {
                    return $response->withJson(array('success' => false));
                }
            })->setName('client-auth');

            $app->get('/provider/auth', function (Request $request, Response $response, array $args) use ($cnt) {
                $params = $request->getQueryParams();
                if ($params) {
                    $username = $params['username'];
                    $password = $params['password'];

                    $session = new M_Auth($cnt, 'login', $username, $password);

                    if ($session->isUserLoggedIn()) {
                        return $response->withJson(array('success' => true), 200);
                    } else {
                        return $response->withJson(array('success' => false));
                    }
                } else {
                    return $response->withJson(array('success' => false));
                }
            })->setName('provider-auth');

            // Handles User logout
            $app->get('/logout', function (Request $request, Response $response, array $args) use ($cnt) {
                $session = new M_Auth($cnt, null, 'logout');
                return $response->withRedirect('/');
            })->setName('logout');

            // Admin Routes
            $app->group('', function(App $app) {
                $app->map(['GET', 'DELETE', 'PATCH', 'PUT'], '', function ($request, $response, $args) {
                    // Find, delete, patch or replace user identified by $args['id']
                })->setName('admin');
                $app->get('/reset-password', function ($request, $response, $args) {
                    // Route for /users/{id:[0-9]+}/reset-password
                    // Reset the password for user identified by $args['id']
                })->setName('admin-password-reset');

                $app->get('/', function (Request $request, Response $response, array $args) {
                    $this->view->addData(['username' => $this->session->user_name]);
                    return $this->view->render('admin::index');
                })->setName('admin-index')->add(new \MmanagerPOS\Middleware\Auth($app->getContainer()->get('router'), 'admin'));
            });



            // CLient routes
            $app->group('/client/{id:[0-9]+}', function (App $app) {
                $app->map(['GET', 'DELETE', 'PATCH', 'PUT'], '', function ($request, $response, $args) {
                    // Find, delete, patch or replace user identified by $args['id']
                })->setName('client');
                $app->get('/reset-password', function ($request, $response, $args) {
                    // Route for /users/{id:[0-9]+}/reset-password
                    // Reset the password for user identified by $args['id']
                })->setName('client-password-reset');
                $app->get('/home', function ($request, $response, $args) {
                    return $this->view->render('client::index');
                })->setName('client-home')->add(new \MmanagerPOS\Middleware\Auth($app->getContainer()->get('router'), 'client'));
            });

            return $app;
        };
    }
}
