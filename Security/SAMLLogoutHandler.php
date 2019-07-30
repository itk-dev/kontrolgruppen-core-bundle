<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Logout\LogoutHandlerInterface;

class SAMLLogoutHandler implements LogoutHandlerInterface
{
    /** @var \Kontrolgruppen\CoreBundle\Security\SAMLAuthenticator */
    private $saml;

    public function __construct(SAMLAuthenticator $saml)
    {
        $this->saml = $saml;
    }

    public function logout(
        Request $request,
        Response $response,
        TokenInterface $token
    ) {
        if ($this->saml->supportsSingleLogout()) {
            $this->saml->getAuth()->logout();
        }
    }
}
