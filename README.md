ZfcUserOnelogin
================

Zend Framework ZfcUser Extension to provide Onelogin SAML Authentication



## Features
- Provides an adapter chain for SAML authentication.
...
- Provides an identity provider for BjyAuthorize
 
## TODO / In Progress
...


## Setup

The following steps are necessary to get this module working

  1. Run `php composer.phar require opsway/zfc-user-onelogin:dev-master`
  2. Add `ZfcUserOnelogin` to the enabled modules list after ZfcUser module (Requires ZfcUser to be activated also)
  3. Copy configuration `zfcuser-onelogin.local.php.dist` to your autoload `config/autoload/zfcuser-onelogin.local.php`
  4. Write correct Onelogin settings

## BjyAuthorize Setup

