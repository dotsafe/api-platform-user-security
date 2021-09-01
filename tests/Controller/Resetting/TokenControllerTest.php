<?php

/*
 * This file is part of the ApiPlatformUserSecurity project.
 *
 * (c) Vincent Touzet <vincent.touzet@dotsafe.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dotsafe\ApiPlatformUserSecurityBundle\Tests\Controller\Resetting;

use Dotsafe\ApiPlatformUserSecurityBundle\Controller\Resetting\TokenController;
use Dotsafe\ApiPlatformUserSecurityBundle\Doctrine\Behaviors\PasswordResettable;
use Dotsafe\ApiPlatformUserSecurityBundle\Doctrine\Behaviors\PasswordResettableTrait;
use Dotsafe\ApiPlatformUserSecurityBundle\Dto\Resetting\Token;
use Dotsafe\ApiPlatformUserSecurityBundle\Manager\ResettingManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;

class TokenControllerTest extends TestCase
{
    public function testICantRetrieveTokenIfUserNotFound()
    {
        $manager = $this->createMock(ResettingManagerInterface::class);
        $manager->expects($this->once())->method('loadUserByResetToken')->willReturn(null);

        $controller = new TokenController($manager);
        $token = new Token('the_token');
        $response = $controller->__invoke($token);

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testICannotRetrieveTokenIfExpired()
    {
        $user = new class() implements PasswordResettable {
            use PasswordResettableTrait;
        };
        $manager = $this->createMock(ResettingManagerInterface::class);
        $manager->expects($this->once())->method('loadUserByResetToken')->willReturn($user);
        $manager->expects($this->once())->method('isTokenExpired')->willReturn(true);

        $controller = new TokenController($manager);
        $token = new Token('the_token');
        $response = $controller->__invoke($token);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testICanGetToken()
    {
        $user = new class() implements PasswordResettable {
            use PasswordResettableTrait;
        };
        $manager = $this->createMock(ResettingManagerInterface::class);
        $manager->expects($this->once())->method('loadUserByResetToken')->willReturn($user);
        $manager->expects($this->once())->method('isTokenExpired')->willReturn(false);

        $controller = new TokenController($manager);
        $token = new Token('the_token');
        $response = $controller->__invoke($token);

        $this->assertEquals($token, $response);
    }
}
