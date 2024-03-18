<?php

namespace App\Controller;

use App\Traits\AppTrait;
use App\Security\ApiCallService;
use App\Types\HTTPMethodType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    use AppTrait;
       
    public function __construct(
        private ApiCallService $apiCallService,
        private PaginatorInterface $paginator
    )
    {
        $this->apiCallService = $apiCallService;
        $this->paginator = $paginator;
    }

    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils, string $dashboard): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute($dashboard);
        }
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route('/logout', name: 'app_logout', options:['expose' => true])]
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route('/users', name: 'app_users', options:['expose' => true])]
    public function users(Request $request)
    {

        $res = $this->apiCallService->sendRequestToApi(HTTPMethodType::GET, '/api/v1/users');
        if ($res!= []) {
            $data = $this->sortDataArray($request, $res['data']['content']);
        } else {
            $data = $this->sortDataArray($request, $res);
        }

         $pagination = $this->paginator->paginate(
                $data,
                $request->query->getInt('page', 1),
            );

            $resActions = $this->apiCallService->sendRequestToApi(HTTPMethodType::GET, '/api/v1/actions');
            if ($resActions!= []) {
                $actions = $this->sortDataArray($request, $resActions['data']['content']);
            } else {
                $actions = $this->sortDataArray($request, $resActions);
            }    

        return $this->render('security/users.html.twig', [
            'menuParent' => 'users',
            'menuChild' => '',
            'titleParent' => 'Utilisateurs',
            'titleChild' => 'Liste',
            'data' => $pagination,
            'actions' => $actions
        ]);
    }

    #[Route('/profils', name: 'app_profils', options:['expose' => true])]
    public function profils(Request $request)
    {
        $res = $this->apiCallService->sendRequestToApi(HTTPMethodType::GET, '/api/v1/profiles');
        if ($res!= []) {
            $data = $this->sortDataArray($request, $res['data']['content']);
        } else {
            $data = $this->sortDataArray($request, $res);
        }

         $pagination = $this->paginator->paginate(
                $data,
                $request->query->getInt('page', 1),
            );
        
        return $this->render('security/profils.html.twig', [
            'menuParent' => 'profils',
            'menuChild' => '',
            'titleParent' => 'Profils',
            'titleChild' => 'Liste',
            'data' => $pagination
        ]);
    }

    #[Route('/profils/add', name: 'app_profils_add', options:['expose' => true])]
    public function addProfils(Request $request)
    {
        $body = $request->request->all();
       $this->apiCallService->sendRequestToApi(HTTPMethodType::POST, '/api/v1/profiles', $body);
    
        return $this->redirectToRoute('app_profils');

    }

    

    #[Route('/actions/assign', name: 'app_assign_actions', options:['expose' => true])]
    public function assignAction(Request $request)
    {
        $body = $request->request->all();
       $this->apiCallService->sendRequestToApi(HTTPMethodType::PUT, '/api/v1/assign-action', $body);
    
        return $this->redirectToRoute('app_users');

    }
    
    #[Route('/actions/add', name: 'app_actions_add', options:['expose' => true])]
    public function addActions(Request $request)
    {
        $body = $request->request->all();
       $this->apiCallService->sendRequestToApi(HTTPMethodType::POST, '/api/v1/actions', $body);
    
        return $this->redirectToRoute('app_actions');

    }

    #[Route('/permissions', name: 'app_permissions', options:['expose' => true])]
    public function permissions()
    {
        $data = [];
        return $this->render('security/permissions.html.twig', [
            'menuParent' => 'permissions',
            'menuChild' => '',
            'titleParent' => '',
            'titleChild' => '',
            'data' => $data
        ]);
    }

    #[Route('/actions', name: 'app_actions', options:['expose' => true])]
    public function actions(Request $request)
    {
        $res = $this->apiCallService->sendRequestToApi(HTTPMethodType::GET, '/api/v1/actions');
        if ($res!= []) {
            $data = $this->sortDataArray($request, $res['data']['content']);
        } else {
            $data = $this->sortDataArray($request, $res);
        }

         $pagination = $this->paginator->paginate(
                $data,
                $request->query->getInt('page', 1),
            );
        
        return $this->render('security/actions.html.twig', [
            'menuParent' => 'actions',
            'menuChild' => '',
            'titleParent' => 'Actions',
            'titleChild' => 'liste',
            'data' => $pagination
        ]);
    }
}
