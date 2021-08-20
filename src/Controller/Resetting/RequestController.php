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

use Dotsafe\ApiPlatformUserSecurityBundle\Doctrine\Behaviors\PasswordResettable;
use Dotsafe\ApiPlatformUserSecurityBundle\Dto\Resetting\Request;
use Dotsafe\ApiPlatformUserSecurityBundle\Manager\ResettingManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class RequestController
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
     * @param Request $data
     */
    public function __invoke($data, UserProviderInterface $userProvider)
    {
        // retrieve user from provider
        try {
            $user = $userProvider->loadUserByUsername($data->email);
        } catch (AuthenticationException $e) {
            return new Response('', Response::HTTP_NO_CONTENT);
        }

        if (null === $user) {
            // user not found
            // send 204 response
            return new Response('', Response::HTTP_NO_CONTENT);
        }

        if (!$user instanceof PasswordResettable) {
            throw new \LogicException('The user must implements the '.PasswordResettable::class.' interface');
        }

        if (!$this->manager->canRequestResetting($user)) {
            return new Response('', Response::HTTP_NO_CONTENT);
        }

        $this->manager->requestResetting($user);

        return new Response('', Response::HTTP_NO_CONTENT);
    }
}
