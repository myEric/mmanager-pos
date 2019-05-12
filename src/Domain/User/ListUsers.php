<?php

declare(strict_types=1);

namespace MmanagerPOS\Domain\User;

use MmanagerPOS\Persistence\Doctrine\UserRepository;
use Doctrine\ORM\EntityManager;
use Slim\Http;

class ListUsers
{
    /**
     * @var EntityManager
     */
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function __invoke(Http\Request $request, Http\Response $response): Http\Response
    {
        /** @var UserRepository[] $users */
        $users = $this->em
            ->getRepository(UserRepository::class)
            ->findAll();

        return $response->withJson($users, 200);
    }
}
