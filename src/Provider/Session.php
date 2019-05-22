<?php

declare(strict_types=1);

namespace MmanagerPOS\Provider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class Session implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(Container $cnt)
    {
        $cnt['session'] = function ($cnt) {
            return new \SlimSession\Helper;
        };
    }
}
