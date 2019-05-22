<?php

declare(strict_types=1);

namespace MmanagerPOS\Provider;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Cache\FilesystemCache;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use PhpRbac\Rbac;

/**
 * A ServiceProvider for registering services related to
 * Doctrine in a DI container.
 *
 * If the project had custom repositories (e.g. UserRepository)
 * they could be registered here.
 */
class MRbac implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(Container $cnt)
    {
        $cnt['rbac'] = function (Container $cnt): LoggerInterface {
             $rbac = new Rbac();
             return $rbac;
        };
    }
}
