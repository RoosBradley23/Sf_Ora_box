<?php
namespace App\Handler;

use App\Security\User;
use App\Types\HTTPMethodType;
use App\Security\ApiCallService;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class SessionIdleHandler implements EventSubscriberInterface
{
    private $requestStack;
    private $securityToken;
    private $router;
    private $maxIdleTime;
    private $apiCallService;

    public function __construct(
        RequestStack $requestStack,
        TokenStorageInterface $securityToken, 
        RouterInterface $router,
        ApiCallService $apiCallService,
        $maxIdleTime = 0
        )
    {
        $this->requestStack = $requestStack;
        $this->securityToken = $securityToken;
        $this->router = $router;
        $this->apiCallService = $apiCallService;
        $this->maxIdleTime = $maxIdleTime;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        if (HttpKernelInterface::MAIN_REQUEST != $event->getRequestType()){
            return;
        }
        $session = $this->requestStack->getSession();
        $route = $event->getRequest()->attributes->get('_route' );
        if ($this->maxIdleTime > 0 && !in_array($route, ['app_login'])) {
            $session->start();
            $lapse = time() - $session->getMetadataBag()->getLastUsed();
            if($this->securityToken->getToken() != null){
                $user = $this->securityToken->getToken()->getUser();
                if ($user instanceof User && $session instanceof Session) {
                    if($lapse > (int)$user->getExpiresIn()){
                        //session is not used since expires_in
                        $session->invalidate();
                        $this->securityToken->setToken(null);
                        $minutes = gmdate("H:i:s", $lapse);
                        $session->getFlashBag()->add('info', "Vous avez été déconnecté après ".$minutes." d'inactivité.");
                        $event->setResponse(new RedirectResponse($this->router->generate('app_login')));
                    }else{
                        //token if expired
                        $tokenExpired = (time() - $user->getTokenCreatedAt()) > $user->getExpiresIn() ? true : false;
                        if($tokenExpired){
                            $body = [
                                "grant_type" => "refresh_token",
                                "client_id" => $_ENV['KEYCLOCK_CLIENT_ID'],
                                "refresh_token" => $user->getRefreshToken()
                            ];
                            $response = $this->apiCallService->sendAuthRequest(HTTPMethodType::POST, $_ENV['KEYCLOCK_LOGIN_URL'], $body);
                            if($response != false && array_key_exists('access_token', $response)){
                                $user->setAccessToken($response['access_token']);
                                $user->setExpiresIn($response['expires_in']);
                                $user->setRefreshExpiresIn($response['refresh_expires_in']);
                                $user->setRefreshToken($response['refresh_token']);
                                $user->setTokenType($response['token_type']);
                                $user->setNotBeforePolicy($response['not-before-policy']);
                                $user->setSessionState($response['session_state']);
                                $user->setScope($response['scope']);
                                $user->setTokenCreatedAt(time());
                            }
                        }
                    }
                }
            }
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            RequestEvent::class => 'onKernelRequest',
        ];
    }
}