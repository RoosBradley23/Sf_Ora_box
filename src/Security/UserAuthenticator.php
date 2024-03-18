<?php

namespace App\Security;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class UserAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;
    
    public const LOGIN_ROUTE = 'app_login';

    private $urlGenerator;

    private $security;

    private $dashboard;

    public function __construct(
        UrlGeneratorInterface $urlGenerator,
        Security $security,
        string $dashboard
    ) {
        $this->urlGenerator = $urlGenerator;
        $this->security = $security;
        $this->dashboard = $dashboard;
    }
    
    public function authenticate(Request $request): SelfValidatingPassport
    {
        $email = $request->request->get('email');
        $password = $request->request->get('password');
        $csrfToken = $request->request->get('_csrf_token');
        $request->getSession()->set(Security::LAST_USERNAME, $email);
        return new SelfValidatingPassport(
            new UserBadge($email),
            [
                new CsrfTokenBadge('authenticate', $csrfToken),
                new CheckCredentialsFromAPIBadge($email, $password)
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }
        return new RedirectResponse($this->urlGenerator->generate($this->dashboard));
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }

    public function supports(Request $request): bool
    {
        return $request->isMethod('POST') && $this->getLoginUrl($request) === $request->getRequestUri();
    }
}
