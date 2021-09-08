<?php

/*
 * This file is part of the ApiPlatformUserSecurity project.
 *
 * (c) Vincent Touzet <vincent.touzet@dotsafe.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dotsafe\ApiPlatformUserSecurityBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class ApiPlatformUserSecurityExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('api_platform_user_security.user_class', $config['user_class']);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        if ($config['resetting']['enabled']) {
            $container->setParameter('api_platform_user_security.resetting.request_path', $config['resetting']['request_path']);
            $container->setParameter('api_platform_user_security.resetting.token_path', $config['resetting']['token_path']);
            $container->setParameter('api_platform_user_security.resetting.reset_path', $config['resetting']['reset_path']);
            $container->setParameter('api_platform_user_security.resetting.token_ttl', $config['resetting']['token_ttl']);
            $loader->load('resetting.yaml');
        }

        if ($config['password_change']['enabled']) {
            $container->setParameter('api_platform_user_security.password_change_path', $config['password_change']['path']);
            $loader->load('password_change.yaml');
        }
    }
}
