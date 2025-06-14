<?php

namespace App\Controller;
use App\Entity\Quiz;
use App\Entity\QuizQuestion;

use App\Model\QuizQuestionModel;
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




    #[Route('/api/teacher/quiz/create', name: 'app_teacher_quiz_create', methods: ['POST'])]
public function createQuiz(Request $request): JsonResponse
{
    $data = json_decode($request->getContent(), true);

    if (!isset($data['name']) || empty($data['name'])) {
        return new JsonResponse(['error' => 'Name is required'], 400);
    }

    $quiz = new Quiz();
    $quiz->setName($data['name']);
    $quiz->setTeacher($this->getUser()); // setăm profesorul curent ca owner

    try {
        $this->entityManager->persist($quiz);
        $this->entityManager->flush();
    } catch (\Exception $e) {
        return new JsonResponse(['error' => 'Failed to save Quiz: ' . $e->getMessage()], 500);
    }

    // Returnăm id-ul și numele quiz-ului creat
    return new JsonResponse([
        'id' => $quiz->getId(),
        'name' => $quiz->getName()
    ]);
}


            


    #[Route('/api/teacher/quiz_question/add/{id}', name: 'app_teacher_quiz_question_add', methods: ['POST'])]

public function addQuizQuestion(Request $request, int $id): Response
{
    try {
        $body = json_decode($request->getContent(), true);
    } catch (\Exception $exception) {
        return new JsonResponse(['error' => 'Malformed Json'], Response::HTTP_BAD_REQUEST);
    }

    $quiz = $this->entityManager->getRepository(Quiz::class)->find($id);
    if (!$quiz) {
        return new JsonResponse(['error' => 'Quiz not found'], Response::HTTP_NOT_FOUND);
    }

    // Creăm modelul și setăm quiz-ul
    $quizQuestionModel = new QuizQuestionModel();
    $quizQuestionModel->question = $body['question'] ?? null;
    $quizQuestionModel->possibleAnswers = $body['possibleAnswers'] ?? null;
    $quizQuestionModel->correctAnswerIndex = $body['correctAnswerIndex'] ?? null;
    $quizQuestionModel->quiz = $quiz;

    $errors = $this->validator->validate($quizQuestionModel);
    if (count($errors) > 0) {
        return new JsonResponse(['error' => (string) $errors], Response::HTTP_BAD_REQUEST);
    }

    try {
        $quizQuestion = $this->autoMapper->map($quizQuestionModel, QuizQuestion::class);
        $this->entityManager->persist($quizQuestion);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Success', 'id' => $quizQuestion->getId()]);
    } catch (\Exception $exception) {
        return new JsonResponse(['error' => 'A intervenit o eroare la salvarea in baza de date'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}





#[Route('/api/teacher/quizzes/ids', name: 'app_teacher_quizzes_ids', methods: ['GET'])]
public function listUserQuizIds(): JsonResponse
{
    $user = $this->getUser();
    if (!$user) {
        return new JsonResponse(['error' => 'User not authenticated'], 401);
    }

    $quizzes = $this->entityManager->getRepository(Quiz::class)->findBy(['teacher' => $user]);

    $ids = array_map(fn($quiz) => ['id' => $quiz->getId()], $quizzes);

    return new JsonResponse($ids);
}





#[Route('/api/teacher/quiz_question/view/{id}', name: 'app_teacher_quiz_question_view', methods: ['GET'])]
public function viewQuizQuestions(int $id): JsonResponse
{
    $quiz = $this->entityManager->getRepository(Quiz::class)->find($id);

    if (!$quiz) {
        return new JsonResponse(['error' => 'Quiz not found'], Response::HTTP_NOT_FOUND);
    }

    // Verificăm dacă userul curent este profesorul acestui quiz
    $user = $this->getUser();
    if ($quiz->getTeacher() !== $user) {
        return new JsonResponse(['error' => 'Access denied'], Response::HTTP_FORBIDDEN);
    }

    $questions = $quiz->getQuizQuestions();


    $result = [];
    foreach ($questions as $question) {
        $result[] = [
            'id' => $question->getId(),
            'question' => $question->getQuestion(),
            'possibleAnswers' => $question->getPossibleAnswers(),
            'correctAnswerIndex' => $question->getCorrectAnswerIndex(),
        ];
    }

    return new JsonResponse($result);
}




#[Route('/api/public/quiz_question', name: 'app_public_quiz_question_list', methods: ['GET'])]
public function listAllQuizQuestions(): JsonResponse
{
    $quizQuestions = $this->entityManager->getRepository(QuizQuestion::class)->findAll();

    $result = [];

    foreach ($quizQuestions as $question) {
        $result[] = [
            'id' => $question->getId(),
            'question' => $question->getQuestion(),
            'possibleAnswers' => $question->getPossibleAnswers(),
            'correctAnswerIndex' => $question->getCorrectAnswerIndex(),
            'quizId' => $question->getQuiz()?->getId(),
        ];
    }

    return new JsonResponse($result);
}




   #[IsGranted('IS_RESOURCE_OWNER', subject: 'quiz', message: 'Logged in user does not own resource')]
#[Route('/api/teacher/quiz/delete/{id}', name: 'app_teacher_quiz_delete', requirements: ['id' => '\d+'], methods: ['DELETE'])]
public function deleteQuiz(Quiz $quiz): JsonResponse
{
    try {
        $this->entityManager->remove($quiz);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Quiz deleted successfully']);
    } catch (\Exception $e) {
        return new JsonResponse(['error' => 'An error occurred while deleting the quiz'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}

}