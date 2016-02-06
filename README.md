TestBundle
==========

This bundle provides some abstract test classes, which are useful and reusable.

Ensure that your config_test.yml have the following configured options (skip web_profiler and|or swiftmailer options if you don't use them):

```yaml
framework:
    test: ~
    session:
        storage_id: session.storage.mock_file
    profiler:
        collect: false

web_profiler:
    toolbar: false
    intercept_redirects: false

swiftmailer:
    disable_delivery: true
```

AbstractSymfonyTest
-------------------

The AbstractSymfonyTest class boots the kernel and give access to the container and the doctrine entity manager.


AbstractControllerTest
----------------------

The AbstractControllerTest class extends the AbstractSymfonyTest and give access to the HttpKernel\Client.
To use the client you have to (re-)configure your security providers by adding a chain provider which provides your original security provider as well as an in memory provider with default configuration. 

```yaml
security:
    providers:
        chain_provider:
            chain:
                providers: [default_provider]
        default_provider:
            id: security_user_provider
        in_memory_provider:
            memory: ~
```

In addition, you have to add the following block to your config_test.yml. You can add or change the user roles, but the username/password configuration is currently hardcoded in the AbstractControllerTest class. 

```yaml
security:
    encoders:
        Symfony\Component\Security\Core\User\User: plaintext

    firewalls:
        secured_area:
            http_basic: ~
    providers:
        chain_provider:
            chain:
                providers: [in_memory_provider]
        in_memory:
            memory:
                users:
                    test:
                        password: test
                        roles: 'ROLE_USER'
```


AbstractTypeTestCase
--------------------

To test your form types you can use the AbstractTypeTestCase. Since it extends from AbstractSymfonyTest the validation is enabled by default and resolving form types from services.yml is possible.
Ensure you update the framework block in config_test.yml to disable csrf_protection in validation tests.

```yaml
framework:
    csrf_protection:
        enabled: false
```