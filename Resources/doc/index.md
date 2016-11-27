Installation
============

Step 1: Download the Bundle
---------------------------

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
$ composer require kraken-collective/ws-symfony-bundle "~1"
```

This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

Step 2: Enable the Bundle
-------------------------

Then, enable the bundle by adding it to the list of registered bundles
in the `app/AppKernel.php` file of your project:

```php
<?php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...
            KrakenCollective\WsSymfonyBundle\KrakenCollectiveWsSymfonyBundle(),
        );

        // ...
    }

    // ...
}
```

Step 3: Create socket listener configuration
--------------------------------------------

```yaml
# app/config.yml

kraken_collective_ws_symfony:
    socket_listener:
        default_listener:
            host: 127.0.0.1
            port: 6080
```

You can define as many socket listeners as you need. The above is just a minimal configuration. For full reference please see [full configuration reference](https://github.com/kraken-collective/ws-symfony/blob/master/Resources/doc/config-reference.md).

Step 4: Create WebSocket server configuration
---------------------------------------------

```yaml
# app/config.yml

kraken_collective_ws_symfony:
    # ...
    server:
        default_server:
            listener: default_listener
            authentication: ~
            routes:
                - "/ws"
```

The WebSocket server will use the default session handler defined in you Symfony application to retrieve the User. By default the User will be authenticated against the `default` firewall.

Advanced configuration
----------------------

#### Firewalls

You can define which firewalls the User should be checked against by defining the `firewall` key in the server config (more than 1 firewall is allowed):

```yaml
# app/config.yml

kraken_collective_ws_symfony:
    # ...
    server:
        default_server:
            # ...
            authentication:
                firewalls:
                    - default
                    - my_custom_firewall
```

#### Anonymous access

By default the anonymous User is not going to be denied the access. If you'd like to change that behaviour you can simply set the `allow_anonymous` option:  

```yaml
# app/config.yml

kraken_collective_ws_symfony:
    # ...
    server:
        default_server:
            # ...
            authentication:
                allow_anonymous: true
```

#### Custom session handler

You can choose which session handler should be used to load the User token from by setting this handlers's service id in the server config:

```yaml
# app/config.yml

kraken_collective_ws_symfony:
    # ...
    server:
        default_server:
            # ...
            session_handler: session-handler.service.id
```

Complete configuration reference
--------------------------------

- https://github.com/kraken-collective/ws-symfony/blob/master/Resources/doc/config-reference.md
