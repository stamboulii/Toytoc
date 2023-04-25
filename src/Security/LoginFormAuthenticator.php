<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\SecurityBundle\Security;
use App\Entity\User;


class LoginFormAuthenticator extends AbstractAuthenticator
{
    public function __construct(private readonly RouterInterface $router)
    {
    }

    public function supports(Request $request): ?bool
    {
        return in_array($request->getPathInfo(), ['/admin/login', '/home/login']) && $request->isMethod('POST');
    }

    public function authenticate(Request $request): Passport
    {
        $email    = $request->request->get('email');
        $password = $request->request->get('password');

        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($password),
            [
                new CsrfTokenBadge(
                    'admin_login',
                    $request->request->get('_csrf_token')
                ),
                (new RememberMeBadge())->enable(),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return new RedirectResponse(
            $token->getUser()->is(User::ROLE_ADMIN) ? $this->router->generate('app_backoffice_index') : $this->router->generate('app_front')
        );
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $request->getSession()->set(Security::AUTHENTICATION_ERROR, $exception);
        return new RedirectResponse(
            $this->router->generate('app_backoffice_login')
        );
    }
}
