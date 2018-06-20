# LTIConsumerBundle

This bundle can be used to establish [LTI (version 1)](https://www.imsglobal.org/activity/learning-tools-interoperability) connections to an e-learning tool provider.  

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
# allows you to send custom parameters to the launch action and include them with the LTI request.
intracto_lti.custom_parameters:
    aCustomParameterName: ltiFieldName
intracto_lti.provider:
    keys:
        url: 'launch-url-toolprovider-connect'
        base_url: 'base-url-toolprovider-connect'
        key: key-provided-by-tool-provider
        secret: secret-provided-by-tool-provder
    parameters:
        lti_version: LTI-1p0
        resource_link_id: ToolProvider
        resource_link_title: ToolProvider
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
            'tool_url' => $this->generateUrl(
                'intracto_lti_consumer_launch',
                [
                    'email' => $this->getUser()->getUsername(),
                    'aCustomParameter' => $elearningId,  // This will be sent with the LTI connection as 'ltiFieldName' because of intracto_lti.custom_parameters
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
<iframe class="page" src="{{ tool_url }}" width="100%" height="100%" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>
</body>
</html>

```

### Create a template 'app/Resources/LTIConsumerBundle/views/index.html.twig'

This template will be used by the bundle to do the connect-request and to load the LTI provider into an iframe in your application

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





