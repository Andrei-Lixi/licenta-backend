<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserExtension\TeacherUser;
use App\Model\NewUserModel;
use App\Service\JwtService;
use AutoMapperPlus\AutoMapperInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Constraints\Json;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController extends AbstractController
{
    public function __construct(
        private readonly JwtService $jwtService,
        private readonly SerializerInterface $serializer,
        private readonly AutoMapperInterface $autoMapper,
        private readonly ValidatorInterface $validator,
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $userPasswordHasher
    )
    {
    }

    #[Route(path: '/api/public/user/create', name: 'app_create_user', methods: ['POST'])]
    public function createUser(Request $request): JsonResponse
    {

        try {
            $body = json_decode($request->getContent(), true);
        } catch (\Exception $exception) {
            return new JsonResponse(['err' => 'Malformed Json']);
        }
        /** @var $newUserModel NewUserModel*/
        $newUserModel = $this->autoMapper->map($body, NewUserModel::class);
        $errors = $this->validator->validate($newUserModel);

        if (count($errors) > 0) {
            return new JsonResponse(['error' => (string) $errors]);
        }

        try {
            $user = $newUserModel->toUserEntity($this->userPasswordHasher);
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        } catch (\Exception $exception) {
            return new JsonResponse(['error' => 'A intervenit o eroare'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse([
            'id' => $user->getUserIdentifier()
        ], Response::HTTP_OK);
    }



    #[Route(path: '/api/admin/user/all', name: 'app_admin_user_all', methods: ['GET'])]
        public function getAllUsers(): JsonResponse
        {
            $allUsers = $this->entityManager->getRepository(TeacherUser::class)->findAll();
            $data = $this->serializer->serialize($allUsers, 'json', ['groups' => 'user']);
            return JsonResponse::fromJsonString($data, Response::HTTP_OK);
        }






        #[Route('/api/admin/user/{id}', name: 'app_admin_user_delete', methods: ['DELETE'])]
            public function deleteUser(int $id, EntityManagerInterface $entityManager): JsonResponse
            {
                $user = $entityManager->getRepository(TeacherUser::class)->find($id);

                if (!$user) {
                    return new JsonResponse(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
                }

                $entityManager->remove($user);
                $entityManager->flush();

                return new JsonResponse(['message' => 'User deleted successfully'], Response::HTTP_OK);
            }



            

    #[Route(path: '/api/admin/user/unconfirmed', name: 'app_admin_user_unconfirmed', methods: ['GET'])]
    public function getUnconfirmedUsers(): JsonResponse
    {
        $inactiveTeacherUsers = $this->entityManager->getRepository(TeacherUser::class)->findBy(['active' => false]);
        $data = $this->serializer->serialize($inactiveTeacherUsers, 'json', ['groups' => 'user']);
        return JsonResponse::fromJsonString($data, Response::HTTP_OK);
    }

    #[Route(path: '/api/admin/user/confirm/{id}', name: 'app_admin_user_confirm', requirements: ['id' => Requirement::POSITIVE_INT], methods: ['POST'])]
    public function confirmTeacherUser(TeacherUser $teacherUser): JsonResponse
    {
        if ($teacherUser->isActive()) {
            return new JsonResponse(['error' => 'User already active'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $teacherUser->setActive(true);
        $this->entityManager->flush();

        return new JsonResponse(['ok' => 'Success'], Response::HTTP_OK);
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