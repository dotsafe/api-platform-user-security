<?php

/*
 * This file is part of the ApiPlatformUserSecurity project.
 *
 * (c) Vincent Touzet <vincent.touzet@dotsafe.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dotsafe\ApiPlatformUserSecurityBundle\Dto;

use Dotsafe\ApiPlatformUserSecurityBundle\Validator\Constraints\CurrentUserPassword;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class PasswordChange
{
    /**
     * @var string
     * @CurrentUserPassword()
     */
    public $currentPassword;
    /**
     * @var string
     */
    public $plainPassword;
    /**
     * @var string
     */
    public $plainPasswordConfirmation;

    /**
     * @Assert\Callback()
     */
    public function validate(ExecutionContextInterface $context)
    {
        if ($this->plainPassword !== $this->plainPasswordConfirmation) {
            $context->buildViolation('The new password and its confirmation mismatch.')
                ->setTranslationDomain('ApiPlatformUserSecurityBundle')
                ->atPath('passwordConfirmation')
                ->addViolation();
        }
    }
}
