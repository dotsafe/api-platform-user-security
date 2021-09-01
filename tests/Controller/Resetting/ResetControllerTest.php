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

use Dotsafe\ApiPlatformUserSecurityBundle\Controller\Resetting\ResetController;
use Dotsafe\ApiPlatformUserSecurityBundle\Doctrine\Behaviors\PasswordResettable;
use Dotsafe\ApiPlatformUserSecurityBundle\Doctrine\Behaviors\PasswordResettableTrait;
use Dotsafe\ApiPlatformUserSecurityBundle\Dto\Resetting\Reset;
use Dotsafe\ApiPlatformUserSecurityBundle\Manager\ResettingManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;

class ResetControllerTest extends TestCase
{
    public function testICannotResetIfUserNotFound()
    {
        $manager = $this->createMock(ResettingManagerInterface::class);
        $manager->expects($this->once())->method('loadUserByResetToken')->willReturn(null);

        $controller = new ResetController($manager);
        $token = new Reset('the_token');
        $response = $controller->__invoke($token);

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testICannotResetIfTokenIsExpired()
    {
        $user = new class() implements PasswordResettable {
            use PasswordResettableTrait;
        };
        $manager = $this->createMock(ResettingManagerInterface::class);
        $manager->expects($this->once())->method('loadUserByResetToken')->willReturn($user);
        $manager->expects($this->once())->method('isTokenExpired')->willReturn(true);

        $controller = new ResetController($manager);
        $token = new Reset('the_token');
        $response = $controller->__invoke($token);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testICannotResetIfPasswordMismatch()
    {
        $user = new class() implements PasswordResettable {
            use PasswordResettableTrait;
        };
        $manager = $this->createMock(ResettingManagerInterface::class);
        $manager->expects($this->once())->method('loadUserByResetToken')->willReturn($user);
        $manager->expects($this->once())->method('isTokenExpired')->willReturn(false);

        $controller = new ResetController($manager);
        $token = new Reset('the_token');
        $token->password = 'Password123!';
        $token->passwordConfirmation = 'Azerty123!';
        $response = $controller->__invoke($token);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testICanReset()
    {
        $user = new class() implements PasswordResettable {
            use PasswordResettableTrait;
        };
        $manager = $this->createMock(ResettingManagerInterface::class);
        $manager->expects($this->once())->method('loadUserByResetToken')->willReturn($user);
        $manager->expects($this->once())->method('isTokenExpired')->willReturn(false);
        $manager->expects($this->once())->method('reset');

        $controller = new ResetController($manager);
        $token = new Reset('the_token');
        $token->password = 'Password123!';
        $token->passwordConfirmation = 'Password123!';
        $response = $controller->__invoke($token);

        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }
}
