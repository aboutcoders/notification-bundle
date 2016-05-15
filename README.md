AbcNotificationBundle
=====================

A symfony bundle that adds process control to the [SonataNotificationBundle](https://github.com/sonata-project/SonataNotificationBundle) and thereby allows to start/stop message processing in a continuous integration environment.

Build Status: [![Build Status](https://travis-ci.org/aboutcoders/notification-bundle.svg?branch=master)](https://travis-ci.org/aboutcoders/notification-bundle)

## Installation

Follow the installation instructions of the required third party bundles:

* [AbcProcessControlBundle](https://github.com/aboutcoders/process-control-bundle)
* [SonataNotificationBundle](https://github.com/sonata-project/SonataNotificationBundle)

Add the AbcNotificationBundle to your `composer.json` file

```
php composer.phar require aboutcoders/notification-bundle
```

Include the bundle in the AppKernel.php class

```php
public function registerBundles()
{
    $bundles = array(
        // ...
        new Abc\Bundle\NotificationBundle\AbcNotificationBundle(),
    );

    return $bundles;
}
```

Following the installation instructions of the [SonataNotificationBundle](https://github.com/sonata-project/SonataNotificationBundle) you will extend the bundle as a part of that define the message entity. Since this bundle extends [SonataNotificationBundle](https://github.com/sonata-project/SonataNotificationBundle) you have to extend from this bundle in order to make it work.

```php
class MySonataNotificationBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'AbcNotificationBundle';
    }
}
```