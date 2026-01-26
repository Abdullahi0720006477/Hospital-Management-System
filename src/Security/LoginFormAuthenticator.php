<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;

use Symfony\Component\Security\Http\Util\TargetPathTrait;

class LoginFormAuthenticator extends AbstractAuthenticator implements \Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    public function __construct(private \Symfony\Component\Routing\Generator\UrlGeneratorInterface $urlGenerator)
    {
    }

    public function supports(Request $request): ?bool
    {
        return self::LOGIN_ROUTE === $request->attributes->get('_route')
            && $request->isMethod('POST');
    }

    public function authenticate(Request $request): Passport
    {
        $email = $request->request->get('_username', '');

        $request->getSession()->set(\Symfony\Component\Security\Core\Security::LAST_USERNAME, $email);

        return new \Symfony\Component\Security\Http\Authenticator\Passport\Passport(
            new \Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge($email),
            new \Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials($request->request->get('_password')),
            [
                new \Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new \Symfony\Component\HttpFoundation\RedirectResponse($targetPath);
        }

        $user = $token->getUser();
        $roles = $user->getRoles();

        // High priority: Admin
        if (in_array('ROLE_ADMIN', $roles) || in_array('ROLE_SUPER_ADMIN', $roles)) {
            return new \Symfony\Component\HttpFoundation\RedirectResponse($this->urlGenerator->generate('app_admin_dashboard'));
        }

        // Mid priority: Doctor
        if (in_array('ROLE_DOCTOR', $roles)) {
            return new \Symfony\Component\HttpFoundation\RedirectResponse($this->urlGenerator->generate('app_doctor_dashboard'));
        }

        // Low priority: Patient
        if (in_array('ROLE_PATIENT', $roles)) {
            return new \Symfony\Component\HttpFoundation\RedirectResponse($this->urlGenerator->generate('app_patient_dashboard'));
        }

        // Fallback for anyone else with access
        return new \Symfony\Component\HttpFoundation\RedirectResponse($this->urlGenerator->generate('app_home'));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        if ($request->hasSession()) {
            $request->getSession()->set(\Symfony\Component\Security\Core\Security::AUTHENTICATION_ERROR, $exception);
        }

        return new \Symfony\Component\HttpFoundation\RedirectResponse($this->urlGenerator->generate('app_login'));
    }

    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        return new \Symfony\Component\HttpFoundation\RedirectResponse($this->urlGenerator->generate('app_login'));
    }
}
