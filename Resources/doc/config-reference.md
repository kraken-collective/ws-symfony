```yaml
# Default configuration for extension with alias: "kraken_collective_ws_symfony"
kraken_collective_ws_symfony:
    server:               # Required

        # Prototype: The server name is going to be used to identify this server.
        name:

            # Name of a defined socket_listener.
            listener:             ~ # Required

            # ID of a session handler. WARNING: Native session handlers will not work here. Use external provider, e.g. Redis.
            session_handler:      session.handler
            authentication:

                # Names of the firewalls that the User is going to be authenticated against.
                firewalls:

                    # Default:
                    - default

                # Switch to allow anonymous Users access.
                allow_anonymous:      false

            # Public paths that allow connection to the server.
            routes:               []
    socket_listener:      # Required

        # Prototype: The socket_listener name is going to be used to identify this listener.
        name:
            protocol:             tcp
            host:                 ~ # Required
            port:                 ~ # Required

            # Event loop model that should be used for this listener. Currently only "select_loop" is available.
            loop:                 select_loop # One of "select_loop"
```
