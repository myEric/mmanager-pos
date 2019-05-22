<?php

declare(strict_types=1);

namespace MmanagerPOS\Provider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Psr\Log\LoggerInterface;


class Monolog implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(Container $cnt)
    {
        $cnt['logger'] = function (Container $cnt): LoggerInterface {
            $settings = $cnt['settings']['logger'];
            $logger = new \Monolog\Logger($settings['name']);
            $logger->pushProcessor(new \Monolog\Processor\UidProcessor());
            $logger->pushHandler(new \Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
            return $logger;
        };
    }
}
