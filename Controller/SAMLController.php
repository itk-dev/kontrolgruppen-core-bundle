<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Controller;

use Kontrolgruppen\CoreBundle\Security\SAMLAuthenticator;
use OneLogin\Saml2\Error;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * Class SAMLController.
 *
 * @Route("/saml")
 */
class SAMLController extends AbstractController
{
    /** @var SAMLAuthenticator */
    private $saml;

    /**
     * SAMLController constructor.
     *
     * @param SAMLAuthenticator $saml
     */
    public function __construct(SAMLAuthenticator $saml)
    {
        $this->saml = $saml;
    }

    /**
     * @Route("/login", name="saml_login")
     *
     * @param Request $request
     *
     * @throws Error
     */
    public function login(Request $request)
    {
        $auth = $this->saml->getAuth();

        $targetUrl = '/';
        $auth->login($targetUrl);
    }

    /**
     * @Route("/acs", name="saml_acs")
     *
     * @param Request $request
     */
    public function acs(Request $request)
    {
        throw new \RuntimeException(sprintf('The route %s should be handled by %s', $request->getPathInfo(), SAMLAuthenticator::class));
    }

    /**
     * @Route("/failure", name="saml_failure")
     *
     * @param Request          $request
     * @param SessionInterface $session
     *
     * @return Response
     */
    public function failure(Request $request, SessionInterface $session)
    {
        return $this->render('saml/failure.html.twig', [
            'access_denied_error' => $session->get(Security::ACCESS_DENIED_ERROR),
            'authentication_error' => $session->get(Security::AUTHENTICATION_ERROR),
        ]);
    }

    /**
     * @Route("/metadata", name="saml_metadata")
     *
     * @return Response
     *
     * @throws Error
     */
    public function metadata()
    {
        $auth = $this->saml->getAuth();
        $settings = $auth->getSettings();
        $metadata = $settings->getSPMetadata();
        $errors = $settings->validateMetadata($metadata);
        if (empty($errors)) {
            return new Response($metadata, 200, ['content-type' => 'text/xml']);
        }

        throw new Error(sprintf('Invalid SP metadata: %s', implode(', ', $errors)), Error::METADATA_SP_INVALID);
    }
}
