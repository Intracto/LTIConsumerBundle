<?php

namespace Intracto\LTIConsumerBundle\Services;

use Intracto\LTIConsumerBundle\Services\Oauth\OAuthRequest;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class IMSProvider
{
    /**
     * @var FormFactoryInterface
     */
    private $formBuilder;

    /**
     * @var
     */
    private $config;

    /**
     * @var
     */
    private $parameters;

    /**
     * IMSProvider constructor.
     *
     * @param FormFactoryInterface $formBuilder
     * @param $config
     */
    public function __construct(FormFactoryInterface $formBuilder, $config)
    {
        $this->formBuilder = $formBuilder;

        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);
        $this->config = $resolver->resolve($config['keys']);

        $this->parameters = $config['parameters'];
    }

    public function prepareLaunchRequest(array $extraParameters = array())
    {
        $parameters = array_merge($this->parameters, $extraParameters);

        $oauthRequest = new OauthRequest($parameters, $this->config['url'], $this->config['base_url'], $this->config['key'], $this->config['secret']);
        $oauthRequest->signRequest();

        return $oauthRequest;
    }

    public function buildForm(OAuthRequest $OAuthRequest)
    {
        $form = $this->formBuilder->createNamedBuilder(null, FormType::class, null, array('csrf_protection' => false));

        foreach ($OAuthRequest->getParameters() as $key => $value) {
            $form->add($key, HiddenType::class, [
                'data' => $value,
                'property_path' => $key,
            ]);
        }

        $form->setAction($OAuthRequest->getUrl())
            ->setMethod('POST');

        return $form->getForm()->createView();
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(array(
            'secret',
            'key',
            'url',
            'base_url'
        ));
    }
}
