<?php

namespace App\Controller;

use App\Entity\Lesson;
use App\Entity\UserExtension\TeacherUser;
use App\Model\LessonModel;
use AutoMapperPlus\AutoMapperInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class LessonController extends AbstractController
{
    public function __construct(
        private readonly AutoMapperInterface $autoMapper,
        private readonly ValidatorInterface $validator,
        private readonly EntityManagerInterface $entityManager,
        private readonly SerializerInterface $serializer
    )
    {
    }

    #[Route(path: "/api/teacher/lesson/add", name: "api_lesson_add", methods: ['POST'])]
    public function addLesson(Request $request): JsonResponse
    {
        /* @var TeacherUser $user **/
        $user = $this->getUser();

        try {
            $body = json_decode($request->getContent(), true);
        } catch (\Exception $exception) {
            return new JsonResponse(['err' => 'Malformed Json']);
        }

        $lessonModel = $this->autoMapper->map($body, LessonModel::class);
        $errors = $this->validator->validate($lessonModel);

        if (count($errors) > 0) {
            return new JsonResponse(['error' => (string) $errors]);
        }

        try {
            /* @var Lesson $lesson **/
            $lesson = $this->autoMapper->map($lessonModel, Lesson::class);
            $lesson->setTeacherUser($user);
            $this->entityManager->persist($lesson);
            $this->entityManager->flush();
        } catch (\Exception $exception) {
            return new JsonResponse(['error' => 'A intervenit o eroare']);
        }

        $response = $this->serializer->serialize($lesson, 'json', ['groups' => 'lesson']);
        return JsonResponse::fromJsonString($response);
    }

    #[Route(path: "/api/public/lesson/get/{id}", name: "api_public_lesson_get", requirements: ['id' => Requirement::POSITIVE_INT], methods: ["GET"])]
    public function getLesson(int $id, Request $request): JsonResponse
    {
        $lesson = $this->entityManager->getRepository(Lesson::class)->find($id);
        if (empty($lesson)) {
            return new JsonResponse(['error' => 'Quiz not found']);
        }

        $response = $this->serializer->serialize($lesson, 'json', ['groups' => 'lesson']);
        return JsonResponse::fromJsonString($response);
    }

    #[IsGranted('IS_RESOURCE_OWNER', subject: 'request', message:'Logged in user does not own resource')]
    #[Route(path: "/api/teacher/lesson/remove/{id}", name: "api_public_lesson_remove", requirements: ['id' => Requirement::POSITIVE_INT], methods: ["DELETE"])]
    public function removeLesson (Request $request, Lesson $lesson): JsonResponse
    {
        try{
            $this->entityManager->remove($lesson);
            $this->entityManager->flush();
            return new JsonResponse(['message' => 'Ok']);
        }catch (\Exception $exception) {
            return new JsonResponse(['error' => 'A intervenit o eroare']);
        }
    }





    
}


