# Getting started

## Prerequisites

This bundle requires Symfony 4+ and API Platform 2.1+.

## Installation

    composer require dotsafe/api-platform-user-security-bundle

Symfony flex will automatically register the bundle for you.

## Configuration

You must configure your user class:

```yaml
# config/packages/api_platform_user_security.yaml
api_platform_user_security:
  user_class: 'App\Entity\User'
```

## Usage

* [**Resetting**](./resetting.md): To add the reset password capability to your API
