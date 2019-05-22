<?php

declare(strict_types=1);

namespace MmanagerPOS\Provider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Psr\Log\LoggerInterface;


class PhpRenderer implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(Container $cnt)
    {
        $cnt['renderer'] = function ($cnt) {
            $settings = $cnt['settings']['renderer'];
            return new \Slim\Views\PhpRenderer($settings['template_path']);
        };
    }
}
