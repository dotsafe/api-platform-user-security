<?php

/*
 * This file is part of the ApiPlatformUserSecurity project.
 *
 * (c) Vincent Touzet <vincent.touzet@dotsafe.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dotsafe\ApiPlatformUserSecurityBundle\Tests\EventListener;

use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Mapping\ClassMetadata;
use Dotsafe\ApiPlatformUserSecurityBundle\EventListener\DoctrinePasswordResetListener;
use PHPUnit\Framework\TestCase;

class DoctrinePasswordResetListenerTest extends TestCase
{
    public function testListenerDoesNothingIfClassDoesNotImplementPasswordResettable()
    {
        $metadata = $this->createMock(ClassMetadata::class);
        $refl = $this->createMock(\ReflectionClass::class);
        $metadata->method('getReflectionClass')->willReturn($refl);
        $refl->method('implementsInterface')->willReturn(false);
        $event = $this->createMock(LoadClassMetadataEventArgs::class);
        $event->method('getClassMetadata')->willReturn($metadata);

        $metadata->expects($this->never())->method('hasField');
        $metadata->expects($this->never())->method('mapField');

        $listener = new DoctrinePasswordResetListener();
        $listener->loadClassMetadata($event);
    }

    public function testListenerAddFieldsToMapping()
    {
        $metadata = $this->createMock(ClassMetadata::class);
        $refl = $this->createMock(\ReflectionClass::class);
        $metadata->method('getReflectionClass')->willReturn($refl);
        $refl->method('implementsInterface')->willReturn(true);
        $event = $this->createMock(LoadClassMetadataEventArgs::class);
        $event->method('getClassMetadata')->willReturn($metadata);

        $metadata->expects($this->exactly(3))->method('hasField');
        $metadata->expects($this->exactly(3))->method('mapField');

        $listener = new DoctrinePasswordResetListener();
        $listener->loadClassMetadata($event);
    }
}
