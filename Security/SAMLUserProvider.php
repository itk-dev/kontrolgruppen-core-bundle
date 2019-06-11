<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Security;

use FOS\UserBundle\Model\UserManagerInterface;
use FOS\UserBundle\Security\UserProvider;
use OneLogin\Saml2\Response;
use OneLogin\Saml2\Settings;

class SAMLUserProvider extends UserProvider
{
    /** @var \Kontrolgruppen\CoreBundle\Security\SAMLAuthenticator */
    private $saml;

    public function __construct(UserManagerInterface $userManager, SAMLAuthenticator $saml)
    {
        parent::__construct($userManager);
        $this->saml = $saml;
    }

    public function getUser(string $username, array $credentials)
    {
        $user = $this->findUser($username);

        if (null === $user) {
            $user = $this->userManager->createUser();
            $user->setPlainPassword(uniqid('', true))
                ->setUsername($username)
                ->setEmail($username);
        }

        $response = new Response(new Settings($this->saml->getSettings()), $credentials['SAMLResponse']);
        $attributes = $response->getAttributes();

        // @TODO: Use a mapping for this
        $roles = [['ROLE_USER']];
        if (isset($attributes['roles'])) {
            $roles[] = $attributes['roles'];
        }
        $roles = array_unique(array_merge(...$roles));

        $user
            ->setRoles($roles)
            ->setEnabled(true);

        $this->userManager->updateUser($user);

        return $user;
    }
}
