<?php

declare(strict_types=1);

namespace MmanagerPOS\Provider;

use MmanagerPOS\Domain\User\CreateUser;
use MmanagerPOS\Domain\User\ListUsers;
use Pimple\ServiceProviderInterface;
use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManager;
use Pimple\Container;
use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

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
        $cnt['logger'] = function (Container $cnt): LoggerInterface {
            $settings = $cnt['settings']['logger'];
            $logger = new \Monolog\Logger($settings['name']);
            $logger->pushProcessor(new \Monolog\Processor\UidProcessor());
            $logger->pushHandler(new \Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
            return $logger;
        };
        $cnt['renderer'] = function ($cnt) {
            $settings = $cnt['settings']['renderer'];
            return new \Slim\Views\PhpRenderer($settings['template_path']);
        };

        $cnt[App::class] = function (Container $cnt): App {
            $app = new App($cnt);

            $app->get('/hello', ListUsers::class);
            $app->get('/users', ListUsers::class);
            $app->post('/users', CreateUser::class);

            $app->get('/', function (Request $request, Response $response, array $args) use ($cnt) {
                $cnt->get('logger')->info("Admin Accces Login '/' route");
                return $this->view->render('admin::index');
            })->setName('admin-dashboard');

            return $app;
        };
    }
}
