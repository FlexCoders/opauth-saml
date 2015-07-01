Opauth-Ldap
=============
[Opauth][1] strategy for SAML authentication.

Opauth is a multi-provider authentication framework for PHP.

# Under development, do not use.

Getting started
----------------
1. Install Opauth-Ldap:
   ```bash
   cd path_to_opauth/Strategy
   git clone git://github.com/flexcoders/opauth-saml.git saml
   ```

2. Configure Opauth-Saml strategy.

3. Call it.

You call it like so:
````
// some input vars
$providerName = "Saml";

// prep a config
$config = [
	'provider' => $providerName,
	'username' => $_POST['username'],
	'password' => $_POST['password'],
	'request_uri' => '/current/uri/'.strtolower($providerName),
	'callback_url' => '/your/uri/for/callback/'.strtolower($providerName),
];

// construct the Opauth object
$this->opauth = new \Opauth($config, true);
````

It will attempt an SAML login, and then redirect to the callback url, just like with all other Opauth
strategies, and with a similar response.

Strategy configuration
----------------------

Required parameters:

```php
<?php
'Saml' => array(
)
```

References
----------

License
---------
Opauth-Ldap is MIT Licensed
Copyright © 2015 FlexCoders Ltd (http://flexcoders.co.uk)

[1]: https://github.com/opauth/opauth
