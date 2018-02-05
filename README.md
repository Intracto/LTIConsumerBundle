LTIConsumerBundle
===============

How to install?
---------------
Install the bundle via composer
```
composer require intracto/lti-consumer-bundle
```
Enable the bundle
```
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    // ...

    public function registerBundles()
    {
        $bundles = array(
            // ...,
            new Intracto\LTIConsumerBundle\LTIConsumerBundle(),
        );

        // ...
    }
}
```

Routing:

```
    intracto_lti_consumer_launch:
        path:     /lti/launch
        defaults: { _controller: intracto.lti.controller:launchAction }
```

How to get started?
-------------------

TODO