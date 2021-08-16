# Resetting

## Enable the funtionality

Enable in your configuration:
 
```yaml
# config/packages/api_platform_user_security.yaml
api_platform_user_security:
  resetting:
    enabled: true
```

Add the mapping to Api Platform

```yaml
# config/packages/api_platform.yaml
api_platform:
    mapping:
        paths:
            - '%kernel.project_dir%/src/Entity'
            # Add the resetting functionality
            - '%kernel.project_dir%/lib/dotsafe/api-platform-user-security-bundle/src/Resources/config/api_platform/resetting.yaml'
```

## Configure your User

Your User class MUST implement the _Dotsafe\ApiPlatformUserSecurityBundle\Doctrine\Behaviors\PasswordResettable_ interface.

```php
<?php

namespace App\Entity;

use Dotsafe\ApiPlatformUserSecurityBundle\Doctrine\Behaviors\PasswordResettable;
use Dotsafe\ApiPlatformUserSecurityBundle\Doctrine\Behaviors\PasswordResettableTrait;


class User implements PasswordResettable
{
    // Use the trait to add the attributes and methods
    use PasswordResettableTrait;
}
```

## Update database schema

The PasswordResettable trait adds 3 attributes that need to be created on database. If you're using migrations then you need to generate a new migration and execute it. Otherwise update your database schema.


## Hook into the event

The bundle does not send emails for you. You're in charge of making it. For that you can subscribe to the following events:

* **PasswordResettingPostRequest**: The event is triggered after the reset request is made.
* **PasswordResettingReset**: The event is triggered during the reset, just before the user is updated in database. You are in charge of updating the user's password.

```php
<?php


namespace App\EventListener\Security\Resetting;

use Dotsafe\ApiPlatformUserSecurityBundle\Events\Resetting\PasswordResettingPostRequest;
use Dotsafe\ApiPlatformUserSecurityBundle\Events\Resetting\PasswordResettingReset;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PasswordResettingSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            PasswordResettingPostRequest::class => 'onPasswordResettingPostRequest',
            PasswordResettingReset::class => 'onPasswordResettingReset',
        ];
    }

    public function onPasswordResettingPostRequest(PasswordResettingPostRequest $event)
    {
        $user = $event->getUser();
        // retrieve the user reset token
        $token = $user->getPasswordResetToken();
        // then you can for example to email the user with the token
    }

    public function onPasswordResettingReset(PasswordResettingReset $event)
    {
        $user = $event->getUser();
        // update the user password
        $user->setPlainPassword($event->getNewPassword());
    }
}
```
