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
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class SAMLController.
 *
 * @Route("/saml")
 */
class SAMLController extends AbstractController
{
    /** @var \Kontrolgruppen\CoreBundle\Security\SAMLAuthenticator */
    private $saml;

    public function __construct(SAMLAuthenticator $saml)
    {
        $this->saml = $saml;
    }

    /**
     * @Route("/login", name="saml_login")
     */
    public function login(Request $request)
    {
        $auth = $this->saml->getAuth();

        $targetUrl = '/';
        $auth->login($targetUrl);
    }

    /**
     * @Route("/acs", name="saml_acs")
     */
    public function acs(Request $request)
    {
        throw new \RuntimeException(sprintf('The route %s should be handled by %s', $request->getPathInfo(), SAMLAuthenticator::class));
    }

    /**
     * @Route("/metadata", name="saml_metadata")
     */
    public function metadata()
    {
        $auth = $this->saml->getAuth();
        $settings = $auth->getSettings();
        $metadata = $settings->getSPMetadata();
        $errors = $settings->validateMetadata($metadata);
        if (empty($errors)) {
            return new Response($metadata, 200, ['content-type' => 'text/xml']);
        } else {
            throw new Error('Invalid SP metadata: '.implode(', ', $errors), Error::METADATA_SP_INVALID);
        }
    }
}
