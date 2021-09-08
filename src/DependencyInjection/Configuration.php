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

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('dotsafe_api_platform_user_security');

        $treeBuilder->getRootNode()
            ->children()
                ->scalarNode('user_class')
                    ->isRequired()
                    ->info('Your base User class')
                ->end()
                ->append($this->appendResettingSection())
                ->append($this->appendPasswordChangeSection())
            ->end();

        return $treeBuilder;
    }

    private function appendResettingSection()
    {
        $treeBuilder = new TreeBuilder('resetting');

        $node = $treeBuilder->getRootNode()
            ->canBeEnabled()
            ->children()
                ->scalarNode('request_path')->defaultValue('/security/resetting/request')->end()
                ->scalarNode('token_path')->defaultValue('/security/resetting/token/{id}')->end()
                ->scalarNode('reset_path')->defaultValue('/security/resetting/reset/{id}')->end()
                ->integerNode('token_ttl')
                    ->info('The number of minutes the token is valid.')
                    ->defaultValue(60 * 2)
                ->end()
            ->end();

        return $node;
    }

    private function appendPasswordChangeSection()
    {
        $treeBuilder = new TreeBuilder('password_change');

        $node = $treeBuilder->getRootNode()
            ->canBeEnabled()
            ->children()
                ->scalarNode('path')->defaultValue('/security/password-change')->end()
            ->end();

        return $node;
    }
}
