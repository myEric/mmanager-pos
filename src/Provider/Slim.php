<?php

declare(strict_types=1);

namespace MmanagerPOS\Provider;

use MmanagerPOS\Domain\User\UserSession;
use MmanagerPOS\Domain\User\CreateUser;
use MmanagerPOS\Domain\User\ListUsers;
use Pimple\ServiceProviderInterface;
use Doctrine\ORM\EntityManager;
use Pimple\Container;
use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;
use MmanagerPOS\Middleware\Auth;

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

            $app->get('/user/login', function (Request $request, Response $response, array $args) use ($cnt) {
                return $this->view->render('user::login');
            })->setName('user-login');

            //Handle authentication with post data
            $app->get('/user/auth', function (Request $request, Response $response, array $args) use ($cnt) {
                $params = $request->getQueryParams();
                if ($params) {
                    $username = $params['username'];
                    $password = $params['password'];

                    $session = new UserSession('login', $username, $password);

                    if ($session->isUserLoggedIn()) {
                        return $response->withJson(array('success' => true), 200);
                    } else {
                        return $response->withJson(array('success' => false));
                    }
                } else {
                    return $response->withJson(array('success' => false));
                }
            })->setName('user-auth');

            // Handles User logout
            $app->get('/user/logout', function (Request $request, Response $response, array $args) use ($cnt) {
                $session = new UserSession('logout');
                return $response->withRedirect('/');
            })->setName('user-logout');

            $app->get('/client/login', function (Request $request, Response $response, array $args) use ($cnt) {
                return $this->view->render('client::login');
            })->setName('client-login');
            $app->get('/provider/login', function (Request $request, Response $response, array $args) use ($cnt) {
                return $this->view->render('provider::login');
            })->setName('provider-login');

            $app->group('', function(App $app) {
                $app->get('/', function (Request $request, Response $response, array $args) use ($cnt) {
                    $this->view->addData(['username' => $this->session->user_name]);
                    return $this->view->render('admin::index');
                })->setName('admin-index');
            })->add(new \MmanagerPOS\Middleware\Auth($app->getContainer()->get('router')));

            return $app;
        };
    }
}
