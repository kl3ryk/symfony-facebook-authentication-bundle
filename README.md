# Symfony Facebook Authentication Bundle

This bundle provides **ready to use** Facebook Authentication solution.

## Usage

### Basic configuration

```YAML
# config.yml

facebook:
    application_id: "your_application_id"
    secret: "your_application_secret"
```

```YAML
# security.yml

firewalls:
    your_firewall:
        facebook: true
        pattern: /
```

### Requesting User Permissions

```YAML
# config.yml

facebook:
    application_id: "your_application_id"
    secret: "your_application_secret"
    permissions:
        - example_permission_a
        - example_permission_b
        - example_permission_c
```

```YAML
# security.yml

firewalls:
    your_firewall:
        facebook: true
        pattern: /
```

### Using Custom User Provider

```YAML
# config.yml

facebook:
    application_id: "your_application_id"
    secret: "your_application_secret"
```

```YAML
# security.yml

providers:
    your_user_provider:
        id: your.user.provider.service.id

firewalls:
    your_firewall:
        facebook: true
        pattern: /
        provider: your_user_provider
```

### Using Custom Authentication handlers

```YAML
# config.yml

facebook:
    application_id: "your_application_id"
    secret: "your_application_secret"
```

```YAML
# security.yml

firewalls:
    your_firewall:
        facebook:
            failure_handler: your.authentication.failure_handler
            success_handler: your.authentication.success_handler
        pattern: /
```

### Using Several Facebook Applications

```YAML
# config.yml

facebook:
    application_id: "your_default_application_id"
    secret: "your_default_application_secret"
```

```YAML
# security.yml

firewalls:
    your_foo_firewall:
        facebook: true
        pattern: /foo

    your_bar_firewall:
        facebook:
            application_id: "your_other_application_id"
            secret: "your_other_application_id"
        pattern: /bar

    your_baz_firewall:
        # default APP configuration, custom permissions
        facebook:
            permissions:
                - example_permission_a
                - example_permission_b
                - example_permission_c
        pattern: /baz
```

## Semantic Versioning

This repository follows [Semantic Versioning 2.0.0](http://semver.org/).
