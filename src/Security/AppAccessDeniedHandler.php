<?php

namespace App\Security;

use App\Traits\UserTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;

class AppAccessDeniedHandler implements AccessDeniedHandlerInterface
{

    private $urlGenerator;

    private $dashboard;

    public function __construct(
        UrlGeneratorInterface $urlGenerator,
        string $dashboard
    )
    {   
        $this->urlGenerator = $urlGenerator;
        $this->dashboard = $dashboard;
    }

    public function handle(Request $request, AccessDeniedException $accessDeniedException): ?RedirectResponse
    {
        return new RedirectResponse($this->urlGenerator->generate($this->dashboard));
    }
}