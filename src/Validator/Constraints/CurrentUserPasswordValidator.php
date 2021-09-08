<?php

/*
 * This file is part of the ApiPlatformUserSecurity project.
 *
 * (c) Vincent Touzet <vincent.touzet@dotsafe.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dotsafe\ApiPlatformUserSecurityBundle\Validator\Constraints;

use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class CurrentUserPasswordValidator extends ConstraintValidator
{
    /** @var Security */
    private $security;
    /** @var EncoderFactoryInterface */
    private $encoderFactory;

    public function __construct(Security $security, EncoderFactoryInterface $encoderFactory)
    {
        $this->security = $security;
        $this->encoderFactory = $encoderFactory;
    }

    public function validate($value, Constraint $constraint)
    {
        /** @var UserInterface $user */
        $user = $this->security->getUser();

        $encoder = $this->encoderFactory->getEncoder(get_class($user));

        if (!$encoder->isPasswordValid($user->getPassword(), $value, $user->getSalt())) {
            $this->context->buildViolation('This value should be the user\'s current password.')
                ->setTranslationDomain('ApiPlatformUserSecurityBundle')
                ->addViolation();
        }
    }
}
