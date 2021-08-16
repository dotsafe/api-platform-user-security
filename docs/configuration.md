# Configuration reference

```yaml
# config/packages/api_platform_user_security
dotsafe_api_platform_user_security:

    # Your base User class
    user_class:           ~ # Required
    resetting:
        enabled:              false
        request_path:         /security/resetting/request
        token_path:           '/security/resetting/token/{id}'
        reset_path:           '/security/resetting/reset/{id}'

        # The number of minutes the token is valid.
        token_ttl:            120

```