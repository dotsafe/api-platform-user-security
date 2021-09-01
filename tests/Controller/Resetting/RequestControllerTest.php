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

use Dotsafe\ApiPlatformUserSecurityBundle\Controller\Resetting\RequestController;
use Dotsafe\ApiPlatformUserSecurityBundle\Doctrine\Behaviors\PasswordResettable;
use Dotsafe\ApiPlatformUserSecurityBundle\Doctrine\Behaviors\PasswordResettableTrait;
use Dotsafe\ApiPlatformUserSecurityBundle\Dto\Resetting\Request;
use Dotsafe\ApiPlatformUserSecurityBundle\Manager\ResettingManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class RequestControllerTest extends TestCase
{
    public function testICannotRequestIfUserNotFound()
    {
        $manager = $this->createMock(ResettingManagerInterface::class);
        $userProvider = $this->createMock(UserProviderInterface::class);
        $userProvider->method('loadUserByUsername')->willReturn(null);

        $controller = new RequestController($manager);
        $manager->expects($this->never())->method('canRequestResetting');
        $manager->expects($this->never())->method('requestResetting');

        $request = new Request();
        $request->email = 'user@example.com';
        $response = $controller->__invoke($request, $userProvider);

        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }

    public function testICannotRequestIfUserProviderThrowError()
    {
        $manager = $this->createMock(ResettingManagerInterface::class);
        $userProvider = $this->createMock(UserProviderInterface::class);
        $userProvider->method('loadUserByUsername')->willThrowException(new AuthenticationException());

        $controller = new RequestController($manager);
        $manager->expects($this->never())->method('canRequestResetting');
        $manager->expects($this->never())->method('requestResetting');

        $request = new Request();
        $request->email = 'user@example.com';
        $response = $controller->__invoke($request, $userProvider);

        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }

    public function testIHaveExceptionIfUserDoesNotImplementPasswordResettable()
    {
        $user = new \stdClass();
        $manager = $this->createMock(ResettingManagerInterface::class);
        $userProvider = $this->createMock(UserProviderInterface::class);
        $userProvider->method('loadUserByUsername')->willReturn($user);

        $controller = new RequestController($manager);
        $manager->expects($this->never())->method('canRequestResetting');
        $manager->expects($this->never())->method('requestResetting');

        $request = new Request();
        $request->email = 'user@example.com';
        $this->expectException(\LogicException::class);
        $response = $controller->__invoke($request, $userProvider);
    }

    public function testICannotRequestIfManagerSaysICant()
    {
        $user = new class() implements PasswordResettable {
            use PasswordResettableTrait;
        };
        $manager = $this->createMock(ResettingManagerInterface::class);
        $userProvider = $this->createMock(UserProviderInterface::class);
        $userProvider->method('loadUserByUsername')->willReturn($user);

        $controller = new RequestController($manager);
        $manager->expects($this->once())->method('canRequestResetting')->willReturn(false);
        $manager->expects($this->never())->method('requestResetting');

        $request = new Request();
        $request->email = 'user@example.com';
        $response = $controller->__invoke($request, $userProvider);

        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }

    public function testICanRequest()
    {
        $user = new class() implements PasswordResettable {
            use PasswordResettableTrait;
        };
        $manager = $this->createMock(ResettingManagerInterface::class);
        $userProvider = $this->createMock(UserProviderInterface::class);
        $userProvider->method('loadUserByUsername')->willReturn($user);

        $controller = new RequestController($manager);
        $manager->expects($this->once())->method('canRequestResetting')->willReturn(true);
        $manager->expects($this->once())->method('requestResetting');

        $request = new Request();
        $request->email = 'user@example.com';
        $response = $controller->__invoke($request, $userProvider);

        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }
}
