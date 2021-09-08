<?php

/*
 * This file is part of the ApiPlatformUserSecurity project.
 *
 * (c) Vincent Touzet <vincent.touzet@dotsafe.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dotsafe\ApiPlatformUserSecurityBundle\Tests\DependencyInjection;

use Dotsafe\ApiPlatformUserSecurityBundle\DependencyInjection\ApiPlatformUserSecurityExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Compiler\ResolveChildDefinitionsPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ApiPlatformUserSecurityExtensionTest extends TestCase
{
    public function testICanEnableResetting()
    {
        $container = $this->getContainer([
            'user_class' => 'stdClass',
        ]);

        $this->assertFalse($container->has('api_platform_user_security.resetting.doctrine_listener'));
        $this->assertFalse($container->has('api_platform_user_security.resetting.manager'));
        $this->assertFalse($container->has('api_platform_user_security.resetting.request_controller'));
        $this->assertFalse($container->has('api_platform_user_security.resetting.token_controller'));
        $this->assertFalse($container->has('api_platform_user_security.resetting.reset_controller'));
        $this->assertFalse($container->has('api_platform_user_security.resetting.token_data_provider'));
        $this->assertFalse($container->has('api_platform_user_security.resetting.reset_data_provider'));

        // compile with resetting enabled
        $container = $this->getContainer([
            'user_class' => 'stdClass',
            'resetting' => [
                'enabled' => true,
            ],
        ]);

        $this->assertTrue($container->has('api_platform_user_security.resetting.doctrine_listener'));
        $this->assertTrue($container->has('api_platform_user_security.resetting.manager'));
        $this->assertTrue($container->has('api_platform_user_security.resetting.request_controller'));
        $this->assertTrue($container->has('api_platform_user_security.resetting.token_controller'));
        $this->assertTrue($container->has('api_platform_user_security.resetting.reset_controller'));
        $this->assertTrue($container->has('api_platform_user_security.resetting.token_data_provider'));
        $this->assertTrue($container->has('api_platform_user_security.resetting.reset_data_provider'));
    }

    public function testICanEnablePasswordChange()
    {
        $container = $this->getContainer([
            'user_class' => 'stdClass',
        ]);

        $this->assertFalse($container->has('api_platform_user_security.password_change.manager'));
        $this->assertFalse($container->has('api_platform_user_security.password_change.controller'));
        $this->assertFalse($container->has('api_platform_user_security.password_change.validator'));

        // compile with password_change enabled
        $container = $this->getContainer([
            'user_class' => 'stdClass',
            'password_change' => [
                'enabled' => true,
            ],
        ]);

        $this->assertTrue($container->has('api_platform_user_security.password_change.manager'));
        $this->assertTrue($container->has('api_platform_user_security.password_change.controller'));
        $this->assertTrue($container->has('api_platform_user_security.password_change.validator'));
    }

    private function getContainer(array $config = [])
    {
        $container = new ContainerBuilder();
        $container->registerExtension(new ApiPlatformUserSecurityExtension());
        $container->loadFromExtension('api_platform_user_security', $config);
        $container->getCompilerPassConfig()->setOptimizationPasses([new ResolveChildDefinitionsPass()]);
        $container->getCompilerPassConfig()->setRemovingPasses([]);
        $container->getCompilerPassConfig()->setAfterRemovingPasses([]);
        $container->compile();

        return $container;
    }
}
