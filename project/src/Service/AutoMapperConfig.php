<?php

namespace App\Service;


use App\Entity\Lesson;
use App\Entity\Quiz;
use App\Entity\QuizQuestion;
use App\Model\LessonModel;
use App\Model\QuizModel;
use App\Model\QuizQuestionModel;
use AutoMapperPlus\AutoMapperPlusBundle\AutoMapperConfiguratorInterface;
use AutoMapperPlus\Configuration\AutoMapperConfigInterface;
use AutoMapperPlus\DataType;
use Doctrine\ORM\EntityManagerInterface;

readonly class AutoMapperConfig implements AutoMapperConfiguratorInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager
    )
    {
    }

    public function configure(AutoMapperConfigInterface $config): void
    {
        $config->registerMapping(DataType::ARRAY, QuizModel::class);
        $config->registerMapping(QuizModel::class, Quiz::class);

        $config->registerMapping(DataType::ARRAY, QuizQuestionModel::class)
            ->forMember('quiz', function (array $source) {
                if (!empty($source['quizId'])){
                    return $this->entityManager->getRepository(Quiz::class)->find($source['quizId']);
                }
                return null;
            });
        $config->registerMapping(QuizQuestionModel::class, QuizQuestion::class);

        $config->registerMapping(DataType::ARRAY, LessonModel::class);
        $config->registerMapping(LessonModel::class, Lesson::class);
    }
}