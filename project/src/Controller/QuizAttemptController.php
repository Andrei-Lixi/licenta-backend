<?php

namespace App\Controller;

use App\Entity\QuizAttempt;
use App\Entity\Quiz;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class QuizAttemptController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {}


    
    #[Route('/api/quiz/attempt/{quizId}', name: 'app_quiz_attempt', methods: ['POST'])]
    public function submitQuizAttempt(Request $request, int $quizId): JsonResponse
    {
        $quiz = $this->entityManager->getRepository(Quiz::class)->find($quizId);
        if (!$quiz) {
            return new JsonResponse(['error' => 'Quiz not found'], 404);
        }

        $data = json_decode($request->getContent(), true);
        if (!isset($data['answers']) || !is_array($data['answers'])) {
            return new JsonResponse(['error' => 'Invalid or missing answers'], 400);
        }

        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Unauthenticated'], 401);
        }

        $correctCount = 0;
        $questions = $quiz->getQuizQuestions();
        $total = count($questions);

        foreach ($questions as $question) {
            $questionId = $question->getId();
            $givenAnswer = $data['answers'][$questionId] ?? null;

            if ($givenAnswer !== null && $givenAnswer === $question->getCorrectAnswerIndex()) {
                $correctCount++;
            }
        }

        $attempt = new QuizAttempt();
        $attempt->setUser($user);
        $attempt->setQuiz($quiz);
        $attempt->setCorrectAnswersCount($correctCount);
        $attempt->setTotalQuestions($total);

        $this->entityManager->persist($attempt);
        $this->entityManager->flush();

        return new JsonResponse([
            'message' => 'Attempt saved successfully',
            'correctAnswers' => $correctCount,
            'totalQuestions' => $total,
            'scorePercent' => round(($correctCount / max($total, 1)) * 100, 2)
        ]);
    }




    #[Route('/api/quiz/attempts', name: 'app_quiz_attempts_list', methods: ['GET'])]
public function listUserQuizAttempts(): JsonResponse
{
    $user = $this->getUser();
    if (!$user) {
        return new JsonResponse(['error' => 'Unauthenticated'], 401);
    }

    $attempts = $this->entityManager->getRepository(QuizAttempt::class)
        ->findBy(['user' => $user], ['id' => 'DESC']); 

    $result = [];

    foreach ($attempts as $attempt) {
        $quiz = $attempt->getQuiz();

        $totalQuestions = $attempt->getTotalQuestions();
        $correctAnswers = $attempt->getCorrectAnswersCount();
        $scorePercent = $totalQuestions > 0 ? round(($correctAnswers / $totalQuestions) * 100, 2) : 0;

        $result[] = [
            'quizId' => $quiz->getId(),
            'quizTitle' => $quiz->getName(),
            'correctAnswers' => $correctAnswers,
            'totalQuestions' => $totalQuestions,
            'scorePercent' => $scorePercent,
            'attemptId' => $attempt->getId(),
            'attemptedAt' => $attempt->getAttemptedAt()?->format('Y-m-d H:i:s'),

        ];
    }

    return new JsonResponse($result);
}




}
