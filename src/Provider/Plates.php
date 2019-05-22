<?php

declare(strict_types=1);

namespace MmanagerPOS\Provider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Projek\Slim\Plates as ServiceProvider;
use Slim\App;
use Aura\Intl\TranslatorLocatorFactory;
use Aura\Intl\Package;

/**
 * A ServiceProvider for registering services related
 * to Plates.
 */
class Plates implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(Container $cnt)
    {
        $cnt['view'] = function (Container $cnt): ServiceProvider {
            $config = $cnt['settings']['plates'];
            $adminPath = $cnt['settings']['plates']['adminPath'];
            $userPath = $cnt['settings']['plates']['userPath'];
            $emailPath = $cnt['settings']['plates']['emailPath'];

            $view = new \Projek\Slim\Plates($config);

            // Register a one-off function
            $view->registerFunction('uppercase', function ($string) {
                return strtoupper($string);
            });

            $view->registerFunction('lowercase', function ($string) {
                return strtolower($string);
            });
            $view->registerFunction('fchar', function ($string) {
                return strtoupper($string[0]);
            });
            // Add folders
            $view->addFolder('admin', $adminPath, true);
            $view->addFolder('user', $userPath, true);
            $view->addFolder('emails', $emailPath, true);

            // Set \Psr\Http\Message\ResponseInterface object
            // Or you can optionaly pass `$cnt->get('response')` in `__construct` second parameter
            $view->setResponse($cnt->get('response'));

            // Instantiate and add Slim specific extension
            $view->loadExtension(new \Projek\Slim\PlatesExtension(
                $cnt->get('router'),
                $cnt->get('request')->getUri()
            ));

            return $view;
        };
    }
}