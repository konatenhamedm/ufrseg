<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class LoginFormAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';
    public const LOGIN_ROUTE_SITE = 'site_login_site';
    public const ROUTE_ADMIN = 'site_admin';

    public const DEFAULT_ROUTE = 'app_default';

    public function __construct(private UrlGeneratorInterface $urlGenerator)
    {
    }


    public function supports(Request $request): bool
    {

        $res = [];
        if($request->attributes->get('_route') === "app_login"){
            $res = self::LOGIN_ROUTE === $request->attributes->get('_route')
                && $request->isMethod('POST');
        }else{
            $res = self::LOGIN_ROUTE_SITE === $request->attributes->get('_route')
                && $request->isMethod('POST');
        }
        return  $res;

    }

    public function authenticate(Request $request): Passport
    {
        $username = $request->request->get('username', '');

        $request->getSession()->set(Security::LAST_USERNAME, $username);

        return new Passport(
            new UserBadge($username),
            new PasswordCredentials($request->request->get('password', '')),
            [
                new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {//dd($request->attributes);
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        if($request->attributes->get('_route') == "app_login"){
            return new RedirectResponse($this->urlGenerator->generate(self::DEFAULT_ROUTE));
        }else{
            return new RedirectResponse($this->urlGenerator->generate(self::ROUTE_ADMIN));
        }
        


    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
