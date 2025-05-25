<?php

namespace App\Controller;

use App\Entity\Quiz;
use App\Entity\UserExtension\TeacherUser;
use App\Model\QuizModel;
use AutoMapperPlus\AutoMapperInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class QuizController extends AbstractController
{
    public function __construct(
        private readonly AutoMapperInterface $autoMapper,
        private readonly ValidatorInterface $validator,
        private readonly EntityManagerInterface $entityManager,
        private readonly SerializerInterface $serializer
    )
    {
    }

    #[Route('/api/teacher/quiz/add', name: 'app_teacher_quiz_add', methods: ['POST'])]
    public function addQuiz(Request $request): Response
    {
        try {
            $body = json_decode($request->getContent(), true);
        } catch (\Exception $exception) {
            return new JsonResponse(['error' => 'Malformed Json'], Response::HTTP_BAD_REQUEST);
        }

        $quizModel = $this->autoMapper->map($body, QuizModel::class);
        $errors = $this->validator->validate($quizModel);

        if (count($errors) > 0) {
            return new JsonResponse(['error' => (string) $errors]);
        }

        try {
            /* @var Quiz $quiz**/
            $quiz = $this->autoMapper->map($quizModel, Quiz::class);
            $quiz->setTeacher($this->getUser());
            $this->entityManager->persist($quiz);
            $this->entityManager->flush();
            return new JsonResponse(['message' => 'Success', 'id' => $quiz->getId()]);
        } catch (\Exception $exception) {
            return new JsonResponse(['error' => 'A intervenit o eroare la salvarea in baza de date'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[IsGranted('IS_RESOURCE_OWNER', subject: 'quiz', message:'Logged in user does not own resource')]
    #[Route('/api/teacher/quiz/get', name: 'app_teacher_quiz_get', methods: ['GET'])]
    public function getOwnedQuizzes(): Response
    {
        /* @var TeacherUser $teacherUser * */
        $teacherUser = $this->getUser();
        $ownedQuizzes = $teacherUser->getQuizzes();
        $data = $this->serializer->serialize($ownedQuizzes, 'json', ['groups' => 'quiz']);
        return JsonResponse::fromJsonString($data);
    }

    #[IsGranted('IS_RESOURCE_OWNER', subject: 'quiz', message:'Logged in user does not own resource')]
    #[Route('/api/teacher/quiz/remove/{id}', name: 'app_teacher_quiz_remove', requirements: ['id' => Requirement::POSITIVE_INT], methods: ['DELETE'])]
    public function removeQuiz(Quiz $quiz): Response
    {
        try {
            $this->entityManager->remove($quiz);
            $this->entityManager->flush();
        } catch (\Exception $exception) {
            return new JsonResponse(['error' => 'A intervenit o eroare la salvarea in baza de date']);
        }
        return new Response('work in progress');
    }
}