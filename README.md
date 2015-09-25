Symfony AbcNotificationBundle
==========================

A symfony bundle that adds process control to the SonataNotificationBundle.

## Installation

Add the bundle:

``` json
{
    "require": {
        "aboutcoders/notification-bundle": "~1"
    }
}
```

Enable the bundles in the kernel:

``` php
# app/AppKernel.php
public function registerBundles()
{
    $bundles = array(

        new Abc\Bundle\NotificationBundle\AbcNotificationBundle(),
    );
}
```