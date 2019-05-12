<?php

declare(strict_types=1);

namespace MmanagerPOS\Domain\User;

use MmanagerPOS\Persistence\Doctrine\UserRepository;
use Doctrine\ORM\EntityManager;
use Slim\Http;


class CreateUser
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var Faker\Generator
     */
    private $faker;

    public function __construct(EntityManager $em, Faker\Generator $faker)
    {
        $this->em = $em;
        $this->faker = $faker;
    }

    public function __invoke(Http\Request $request, Http\Response $response): Http\Response
    {
        $newRandomUser = new User($this->faker->name, $this->faker->password);

        $this->em->persist($newRandomUser);
        $this->em->flush();

        return $response->withJson($newRandomUser, 201);
    }
}
