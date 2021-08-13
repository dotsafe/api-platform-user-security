<?php

namespace Dotsafe\ApiPlatformUserSecurityBundle\Controller\Resetting;

use Doctrine\ORM\EntityManagerInterface;
use Dotsafe\ApiPlatformUserSecurityBundle\Dto\Resetting\Token;
use Dotsafe\ApiPlatformUserSecurityBundle\Manager\ResettingManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class ResetController
{
    private ResettingManagerInterface $manager;

    public function __construct(ResettingManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param Token $data
     */
    public function __invoke($data, EntityManagerInterface $entityManager)
    {
        $user = $this->manager->loadUserByResetToken($data->token);

        if (!$user) {
            // user not found
            return new Response('', Response::HTTP_NOT_FOUND);
        }

        // check if token is expired
        if ($this->manager->isTokenExpired($user)) {
            return new Response('', Response::HTTP_BAD_REQUEST);
        }

        // check if passwords match
        if ($data->password !== $data->passwordConfirmation) {
            return new Response('', Response::HTTP_BAD_REQUEST);
        }

        $this->manager->reset($user, $data->password);

        return new Response('', Response::HTTP_NO_CONTENT);
    }
}