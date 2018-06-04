# LTIConsumerBundle

This bundle was created to make an LTI (version 1) connection with [Sofia](https://sofialearn.com). 

## How to install?

Install the bundle via composer
```bash
composer require intracto/lti-consumer-bundle
```
Enable the bundle
```php
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

```yaml
    intracto_lti_consumer_launch:
        path:     /lti/launch
        defaults: { _controller: intracto.lti.controller:launchAction }
```

## How to get started?

### Fill in parameters.yml

```yaml
LTI.sofia:
    keys:
        url: 'launch-url-sofia-connect'
        base_url: 'base-url-sofia-connect'
        key: key-provided-by-sofia
        secret: secret-provided-by-sofia
    parameters:
        lti_version: LTI-1p0
        resource_link_id: Sofia
        resource_link_title: Sofia
        tool_consumer_info_version: '1.1'
        tool_consumer_instance_guid: your-instance-guid
        tool_consumer_instance_description: your-instance-name
```

### Create action in Controller

```php
public function openElearningAction($elearningId)
{

    return $this->render(
        '@App/Elearning/e_learning.html.twig',
        [
            'sofia_url' => $this->generateUrl(
                'intracto_lti_consumer_launch',
                [
                    'email' => $this->getUser()->getUsername(),
                    'courseId' => $elearningId,
                ]
            )
        ]
    );
}

```

### Create a template 'e_learning.html.twig'

```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Elearning</title>
</head>
<body>
<header class="e-learning">
    <h1 class="e-learning__title">{{ 'e_learning.page_title'|trans }}</h1>
</header>
<iframe class="page" src="{{ sofia_url }}" width="100%" height="100%" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>
</body>
</html>

```

### Create a template 'app/Resources/LTIConsumerBundle/views/index.html.twig'

This template will be used by the bundle to do the connect-request and to load the Sofia-app into an I-frame in your application

```html
{{ form(form,{'attr': {'id': 'lti_form'}}) }}
<p>{{ 'lti.launch.message'|trans }}</p>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script>
    (function($) {
        $('#lti_form').submit();
    })(jQuery);
</script>

```





