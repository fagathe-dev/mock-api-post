<?php
namespace App\Controller;

use App\Entity\User;
use App\Service\ApiUserService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

#[Route('/api/users', name: 'api_user_')]
class ApiUserController extends AbstractController {

    public function __construct(private ApiUserService $service)
    {}

    #[Route('', name: 'index', methods: ['GET'])]
    public function apiUserList(Request $request): JsonResponse {
        $response = $this->service->listUser($request);

        return $this->json(
            $response->data,
            $response->status,
            $response->headers,
            ['groups' => ['users:list']]
        );
    }

    #[Route('/{id}', name: 'show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function apiUserShow(User $user):JsonResponse 
    {
        return $this->json(
            $user,
            Response::HTTP_OK,
            [],
            ['groups' => ['users:read']]
        );
    }

}