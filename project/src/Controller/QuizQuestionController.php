<?php

namespace App\Controller;

use App\Entity\QuizQuestion;
use App\Model\QuizQuestionModel;
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

class QuizQuestionController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly AutoMapperInterface $autoMapper,
        private readonly ValidatorInterface $validator,
        private readonly SerializerInterface $serializer
    )
    {
    }

    #[Route('api/teacher/quiz_question/add', name: 'app_teacher_quiz_question_add', methods: ['POST'])]
    public function addQuizQuestion(Request $request): Response
    {
        try {
            $body = json_decode($request->getContent(), true);
        } catch (\Exception $exception) {
            return new JsonResponse(['error' => 'Malformed Json'], Response::HTTP_BAD_REQUEST);
        }

        $quizQuestionModel = $this->autoMapper->map($body, QuizQuestionModel::class);
        $errors = $this->validator->validate($quizQuestionModel);

        if (count($errors) > 0) {
            return new JsonResponse(['error' => (string) $errors]);
        }

        try {
            /* @var QuizQuestion $quizQuestion**/
            $quizQuestion = $this->autoMapper->map($quizQuestionModel, QuizQuestion::class);
            $this->entityManager->persist($quizQuestion);
            $this->entityManager->flush();
            return new JsonResponse(['message' => 'Success', 'id' => $quizQuestion->getId()]);
        } catch (\Exception $exception) {
            return new JsonResponse(['error' => 'A intervenit o eroare la salvarea in baza de date'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[IsGranted('IS_RESOURCE_OWNER', subject: 'quizQuestion', message: 'Logged in user does not own resource')]
    #[Route('api/teacher/quiz_question/get/{id}', name: 'app_teacher_quiz_question_get', requirements: ['id' => Requirement::POSITIVE_INT], methods: ['GET'])]
    public function getQuizQuestion(QuizQuestion $quizQuestion): Response
    {
        $data = $this->serializer->serialize($quizQuestion, 'json', ['groups' => ['quiz_question']]);
        return JsonResponse::fromJsonString($data);
    }

    #[IsGranted('IS_RESOURCE_OWNER', subject: 'quizQuestion', message: 'Logged in user does not own resource')]
    #[Route('api/teacher/quiz_question/delete/{id}', name: 'app_teacher_quiz_question_delete', requirements: ['id' => Requirement::POSITIVE_INT], methods: ['GET'])]
    public function removeQuizQuestion(QuizQuestion $quizQuestion): Response
    {
        try {
            $this->entityManager->remove($quizQuestion);
            $this->entityManager->flush();
            return new JsonResponse(['message' => 'Success']);
        } catch (\Exception $exception) {
            return new JsonResponse(['error' => 'A intervenit o eroare la salvarea in baza de date']);
        }
    }
}