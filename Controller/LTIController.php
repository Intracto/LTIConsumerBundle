<?php

namespace Intracto\LTIConsumerBundle\Controller;

use Intracto\LTIConsumerBundle\Model\LISPerson;
use Intracto\LTIConsumerBundle\Services\IMSProvider;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LTIController extends Controller
{
    private $IMSProvider;

    /**
     * DefaultController constructor.
     *
     * @param $IMSProvider IMSProvider
     */
    public function __construct(IMSProvider $IMSProvider)
    {
        $this->IMSProvider = $IMSProvider;
    }

    public function launchAction(Request $request)
    {
        $person = LISPerson::createFromRequest($request);

        $parameters = array_filter(array_merge(
            $person->getAsParameters(),
            $this->extractCustomParameters($request),
            array('lti_message_type' => 'basic-lti-launch-request')
        ));

        $optionsResolver = new OptionsResolver();
        // Allow additional parameters
        $optionsResolver->setDefined(array_keys($parameters));
        $optionsResolver->setRequired(array(
            'lis_person_contact_email_primary',
        ));
        $optionsResolver->resolve($parameters);

        $oauthRequest = $this->IMSProvider->prepareLaunchRequest($parameters);
        $form = $this->IMSProvider->buildForm($oauthRequest);

        return $this->render('@LTIConsumer/index.html.twig', ['form' => $form]);
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    public function extractCustomParameters(Request $request)
    {
        try {
            $customParameterMapping = $this->getParameter('intracto_lti.custom_parameters');
        } catch (\InvalidArgumentException $e) {
            return [];  // Optional parameter
        }

        $customParameters = array();
        foreach ($customParameterMapping as $requestParam => $ltiParam) {
            $customParameters[$ltiParam] = $request->get($requestParam);
        }

        return $customParameters;
    }
}
