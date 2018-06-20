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
        $customParameters = $this->getParameter('intracto_lti.custom_parameters');

        $person = LISPerson::createFromRequest($request);
        $parameters = $person->getAsParameters();
        $parameters = array_filter(array_merge($parameters, array(
            'custom_courseid' => $request->get('courseId'),
            'lti_message_type' => 'basic-lti-launch-request',
        )));

        $optionsResolver = new OptionsResolver();
        // Allow additional parameters
        $optionsResolver->setDefined(array_keys($parameters));
        $optionsResolver->setRequired(array(
            'lis_person_contact_email_primary',
        ));
        $optionsResolver->resolve($parameters);

        $oauthRequest = $this->IMSProvider->prepareLaunchRequest($parameters);
        $form = $this->IMSProvider->buildForm($oauthRequest);

        return $this->render('LTIConsumerBundle::index.html.twig', ['form' => $form]);
    }
}
