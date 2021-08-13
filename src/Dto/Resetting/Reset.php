<?php

namespace Dotsafe\ApiPlatformUserSecurityBundle\Dto\Resetting;

class Reset extends Token
{
    public ?string $password = null;

    public ?string $passwordConfirmation = null;
}
