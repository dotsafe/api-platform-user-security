<?php

/*
 * This file is part of the ApiPlatformUserSecurity project.
 *
 * (c) Vincent Touzet <vincent.touzet@dotsafe.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dotsafe\ApiPlatformUserSecurityBundle\EventListener;

use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Dotsafe\ApiPlatformUserSecurityBundle\Doctrine\Behaviors\PasswordResettable;

class DoctrinePasswordResetListener
{
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
    {
        $classMetadata = $eventArgs->getClassMetadata();
        if (!$classMetadata->getReflectionClass()->implementsInterface(PasswordResettable::class)) {
            return;
        }

        // add fields if not set
        if (!$classMetadata->hasField('passwordRequestedAt')) {
            $classMetadata->mapField([
                'fieldName' => 'passwordRequestedAt',
                'type' => 'datetime',
                'column_name' => 'password_requested_at',
                'nullable' => true,
            ]);
        }
        if (!$classMetadata->hasField('passwordResetTokenExpiresAt')) {
            $classMetadata->mapField([
                'fieldName' => 'passwordResetTokenExpiresAt',
                'type' => 'datetime',
                'column_name' => 'password_reset_token_expires_at',
                'nullable' => true,
            ]);
        }
        if (!$classMetadata->hasField('passwordResetToken')) {
            $classMetadata->mapField([
                'fieldName' => 'passwordResetToken',
                'type' => 'string',
                'column_name' => 'password_reset_token',
                'nullable' => true,
                'length' => 255,
            ]);
        }
    }
}
