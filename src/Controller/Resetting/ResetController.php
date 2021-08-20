<?php

/*
 * This file is part of the ApiPlatformUserSecurity project.
 *
 * (c) Vincent Touzet <vincent.touzet@dotsafe.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dotsafe\ApiPlatformUserSecurityBundle\Controller\Resetting;

use Dotsafe\ApiPlatformUserSecurityBundle\Dto\Resetting\Token;
use Dotsafe\ApiPlatformUserSecurityBundle\Manager\ResettingManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class ResetController
{
    /**
     * @var ResettingManagerInterface
     */
    private $manager;

    public function __construct(ResettingManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param Token $data
     */
    public function __invoke($data)
    {
        $user = $this->manager->loadUserByResetToken($data->getId());

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
