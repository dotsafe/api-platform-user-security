# PasswordChange

## Setup

### Enable the funtionality

Enable in your configuration:
 
```yaml
# config/packages/api_platform_user_security.yaml
api_platform_user_security:
  password_change:
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
            - '%kernel.project_dir%/lib/dotsafe/api-platform-user-security-bundle/src/Resources/config/api_platform/password_change.yaml'
```

### Hook into the event

The bundle does not update the user's password. To do so you MUST add a listener to the **PasswordChangeOnReset** event.

```php
<?php


namespace App\EventListener\Security\Resetting;

use Dotsafe\ApiPlatformUserSecurityBundle\Events\Resetting\PasswordChangeOnReset;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PasswordResettingSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            PasswordChangeOnReset::class => 'onPasswordChangeOnReset',
        ];
    }

    public function onPasswordChangeOnReset(PasswordChangeOnReset $event)
    {
        $user = $event->getUser();
        $user->setPlainPassword($event->getNewPassword());
    }

}
```

## Usage

Once you've setup the change password functionnality functionality you will see a new endpoints.

| Endpoint                 | Default url                    | description           |
|--------------------------|--------------------------------|-----------------------|
| **POST PasswordChange**  | /security/password-change      | Change the password   |

### PasswordChange

You MUST be logged in to call this endpoint as it is used to change the current user's password.

#### Parameters

| Parameter                 | In   | description                          |
|---------------------------|------|--------------------------------------|
| currentPassword           | body | The user's current password          |
| plainPassword             | body | The user's new password              |
| plainPasswordConfirmation | body | The user's new password confirmation |

#### Responses

| Status code | description                  |
|-------------|------------------------------|
| 204         | The request has been handled |
| 400         | Either the user's current password is wrong or the new password and it's confirmation mismatch |

#### Example

```bash
curl -X POST http://my.api/security/password-change -H 'Authorization: Bearer XXX' -d '{"currentPassword":"1234","plainPassword":"Azerty123!","plainPasswordConfirmation":"Azerty123!"}'
```
