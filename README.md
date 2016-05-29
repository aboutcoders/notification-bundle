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

Following the installation instructions of the [SonataNotificationBundle](https://github.com/sonata-project/SonataNotificationBundle) you will create or generate a bundle within your application that extends from the SonataNotificationBundle. Since this bundle also extends from the SonataNotificationBundle you have to extend your created/generated bundle from this bundle instead of the SonataNotificationBundle.

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

ToDo:

- Provide a pull request for the [SonataNotificationBundle](https://github.com/sonata-project/SonataNotificationBundle) that allows to define custom message managers/iterators and thereby make this bundle obsolete