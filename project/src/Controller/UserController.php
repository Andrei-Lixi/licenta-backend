<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\JwtService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

class UserController extends AbstractController
{
    public function __construct(
        private readonly JwtService $jwtService,
        private readonly SerializerInterface $serializer
    )
    {
    }

    #[Route(path: '/login', name: 'app_login', methods: ['POST'])]
    public function login(): JsonResponse
    {
        /** @Var User $loggedInUser*/
        $loggedInUser = $this->getUser();

        $token = $this->jwtService->generateJwtForUser($loggedInUser);

        return new JsonResponse(
            [
                'jwt' => $token
            ],
            Response::HTTP_OK
        );
    }

    #[Route(path: '/api/me', name: 'api_me', methods: ['GET'])]
    public function me(): JsonResponse
    {
        $loggedInUser = $this->getUser();
        $loggedInUserData = $this->serializer->serialize(
            $loggedInUser,
            'json',
            ['groups' => 'user']
        );

        return JsonResponse::fromJsonString($loggedInUserData);
    }
    #[Route(path: '/api/admin/user/add', name: 'add_user', methods: ['POST'])]
    public function addUser(): Response
    {

        return new JsonResponse(['err' => 'work in progress']);
    }
}