# Resetting

## Setup

### Enable the funtionality

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

### Configure your User

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

### Update database schema

The PasswordResettable trait adds 3 attributes that need to be created on database. If you're using migrations then you need to generate a new migration and execute it. Otherwise update your database schema.


### Hook into the event

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

## Usage

Once you've setup the resetting functionality you will see 3 new endpoints.

| Endpoint          | Default url                    | description           |
|-------------------|--------------------------------|-----------------------|
| **POST Request**  | /security/resetting/request    | Request a reset token |
| **GET Token**     | /security/resetting/token/{id} | Retrieve a token      |
| **POST Reset**    | /security/resetting/reset/{id} | Reset the password    |

### Request

This is the first endpoint you will need to call to request a reset token. The token will NOT be returned by the API. You're in charge of sending the token to the user. See [Hook into the event](#hook-into-the-event).

#### Parameters

| Parameter | In   | description           |
|-----------|------|-----------------------|
| email     | body | The email of the user |

#### Responses

| Status code | description                  |
|-------------|------------------------------|
| 204         | The request has been handled |

For security reasons the endpoint return a 204 status code even if the user is not found in the database.

#### Example

```bash
curl -X POST http://my.api/security/resetting/request -d '{"email":"user@example.com"}'
```

### Token

This endpoint allows you to retrieve a user by its token.

#### Parameters

| Parameter | In   | description           |
|-----------|------|-----------------------|
| id        | url  | The reset token       |

#### Responses

| Status code | description                  |
|-------------|------------------------------|
| 200         | The token is valid           |
| 400         | The token is expired         |
| 404         | The token does not exist     |

For security reasons the endpoint return a 204 status code even if the user is not found in the database.

#### Example

```bash
curl -X GET http://my.api/security/resetting/token/XXX
```

### Reset

This endpoint allows you to reset the user password.

#### Parameters

| Parameter             | In   | description               |
|-----------------------|------|---------------------------|
| id                    | url  | The reset token           |
| password              | body | The new password          |
| passwordConfirmation  | body | The password confirmation |

#### Responses

| Status code | description                                                              |
|-------------|--------------------------------------------------------------------------|
| 204         | The password has been reset                                                       |
| 400         | The token is expired or the password and its confirmation does not match |
| 404         | The token does not exist                                                 |

For security reasons the endpoint return a 204 status code even if the user is not found in the database.

#### Example

```bash
curl -X GET http://my.api/security/resetting/token/XXX -d '{"password":"Azerty123!","passwordConfirmation":"Azerty123!"}'
```
