<?php

/*
 * This file is part of the ApiPlatformUserSecurity project.
 *
 * (c) Vincent Touzet <vincent.touzet@dotsafe.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dotsafe\ApiPlatformUserSecurityBundle\Controller;

use Dotsafe\ApiPlatformUserSecurityBundle\Dto\PasswordChange;
use Dotsafe\ApiPlatformUserSecurityBundle\Manager\PasswordChangeManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class PasswordChangeController
{
    /** @var PasswordChangeManagerInterface */
    private $manager;

    public function __construct(PasswordChangeManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param PasswordChange $data
     */
    public function __invoke($data)
    {
        // if the request is not valid an exception is thrown
        $this->manager->validate($data);

        $this->manager->reset($data->plainPassword);

        return new Response('', Response::HTTP_NO_CONTENT);
    }
}
