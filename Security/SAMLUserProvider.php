<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Security;

use OneLogin\Saml2\Response;
use OneLogin\Saml2\Settings;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class SAMLUserProvider implements UserProviderInterface
{
    /** @var \Kontrolgruppen\CoreBundle\Service\UserManagerInterface */
    private $userManager;

    /** @var \Kontrolgruppen\CoreBundle\Security\SAMLAuthenticator */
    private $saml;

    public function __construct(UserManagerInterface $userManager, SAMLAuthenticator $saml)
    {
        $this->userManager = $userManager;
        $this->saml = $saml;
    }

    public function getUser(string $username, array $credentials)
    {
        $user = $this->userManager->findUserByUsername($username);

        if (null === $user) {
            $user = $this->userManager->createUser();
            $user->setUsername($username);
        }

        $response = new Response(new Settings($this->saml->getSettings()), $credentials['SAMLResponse']);
        $attributes = $response->getAttributes();

        // @TODO: Use a mapping for this
        $roles = [['ROLE_USER']];
        if (isset($attributes['roles'])) {
            $roles[] = $attributes['roles'];
        }
        $roles = array_unique(array_merge(...$roles));

        $user->setRoles($roles);

        $this->userManager->updateUser($user);

        return $user;
    }

    public function loadUserByUsername($username)
    {
        throw new \RuntimeException('Lazy programmer exception: '.__METHOD__.' not implemented!');
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof UserInterface) {
            throw new UnsupportedUserException(sprintf('Expected an instance of %s, but got "%s".', UserInterface::class, \get_class($user)));
        }

        if (!$this->supportsClass(\get_class($user))) {
            throw new UnsupportedUserException(sprintf('Expected an instance of %s, but got "%s".', $this->userManager->getClass(), \get_class($user)));
        }

        if (null === $reloadedUser = $this->userManager->findUserBy(['id' => $user->getId()])) {
            throw new UsernameNotFoundException(sprintf('User with ID "%s" could not be reloaded.', $user->getId()));
        }

        return $reloadedUser;
    }

    public function supportsClass($class)
    {
        $userClass = $this->userManager->getClass();

        return $userClass === $class || is_subclass_of($class, $userClass);
    }
}
