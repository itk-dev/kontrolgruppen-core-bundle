<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Security;

use OneLogin\Saml2\Auth;
use OneLogin\Saml2\Error;
use OneLogin\Saml2\IdPMetadataParser;
use OneLogin\Saml2\Response;
use OneLogin\Saml2\Settings;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class SAMLAuthenticator extends AbstractGuardAuthenticator
{
    /** @var \Symfony\Component\Routing\RouterInterface */
    private $router;

    /** @var array */
    private $settings;

    public function __construct(RouterInterface $router, array $settings)
    {
        $this->router = $router;
        $this->settings = $settings;
    }

    public function start(
        Request $request,
        AuthenticationException $authException = null
    ) {
        $url = $this->router->generate('saml_login');

        return new RedirectResponse($url);
    }

    public function supports(Request $request)
    {
        return '/saml/acs' === $request->getPathInfo()
            && !empty($request->get('SAMLResponse'));
    }

    public function getCredentials(Request $request)
    {
        return [
            'SAMLResponse' => $request->get('SAMLResponse'),
            'RelayState' => $request->get('RelayState'),
        ];
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $auth = $this->getAuth();

        $requestID = $_SESSION['AuthNRequestID'] ?? null;
        $auth->processResponse($requestID);
        if (isset($requestID)) {
            unset($_SESSION['AuthNRequestID']);
        }

        $errors = $auth->getErrors();

        if (!empty($errors)) {
            throw new Error(implode(PHP_EOL, $errors));
        }

        if (!$auth->isAuthenticated()) {
            throw new AuthenticationException('Not authenticated');
        }

        if (!$userProvider instanceof SAMLUserProvider) {
            throw new \RuntimeException(sprintf(
                'Invalid user provider: %s is not an instance of %s',
                \get_class($userProvider),
                SAMLUserProvider::class
            ));
        }

        $username = $auth->getNameId();
        $user = $userProvider->getUser($username, $credentials);

        return $user;
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
    }

    public function onAuthenticationFailure(
        Request $request,
        AuthenticationException $exception
    ) {
        $request->attributes->set(Security::AUTHENTICATION_ERROR, $exception);

        return new RedirectResponse('/');
    }

    public function onAuthenticationSuccess(
        Request $request,
        TokenInterface $token,
        $providerKey
    ) {
        // @TODO: Redirect to originally requested url.
        return new RedirectResponse('/');
    }

    public function supportsRememberMe()
    {
        return false;
    }

    public function getAuth()
    {
        return new Auth($this->getSettings());
    }

    public function getResponse(string $payload)
    {
        return new Response(new Settings($this->getSettings()), $payload);
    }

    public function getSettings()
    {
        if (isset($this->settings['idp']) && \is_string($this->settings['idp'])) {
            if (file_exists($this->settings['idp'])) {
                $data = IdPMetadataParser::parseFileXML($this->settings['idp']);
                if (isset($data['idp'])) {
                    $this->settings['idp'] = $data['idp'];
                }
            }
        }

        return $this->settings;
    }

    public function supportsSingleLogout()
    {
        return false;
    }

    public function getRoles($samlResponse)
    {
        $roles = [];

        $settings = $this->getSettings();
        $response = new Response(new Settings($settings), $samlResponse);
        $attributes = $response->getAttributes();

        if (isset($settings['user_roles'])) {
            $userRoles = $settings['user_roles'];
            $attribute = $userRoles['attribute'] ?? 'http://schemas.xmlsoap.org/claims/Group';

            $samlRoles = $this->getSAMLRoles($attributes, $attribute);
            $rolesMap = $this->getRolesMap($userRoles);

            if (isset($rolesMap['CN'])) {
                $rolesMap = $rolesMap['CN'];
            }

            foreach ($samlRoles as $role) {
                if (isset($rolesMap[$role])) {
                    $roles[] = (array) $rolesMap[$role];
                }
            }
        }

        // Flatten the roles and make unique.
        if (!empty($roles)) {
            $roles = array_values(array_unique(array_merge(...$roles)));
        }

        return $roles;
    }

    private function getSAMLRoles(array $attributes, string $attributeName)
    {
        return $attributes[$attributeName] ?? [];
    }

    private function getRolesMap($settings)
    {
        $map = [];

        if (isset($settings['fields']) && \is_array($settings['fields'])) {
            foreach ($settings['fields'] as $name => $value) {
                $map[$name] = $value;
            }
        }

        return $map;
    }
}
