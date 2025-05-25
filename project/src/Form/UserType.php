<?php

namespace App\Form;

use App\Enum\UserEnum;
use Doctrine\DBAL\Types\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', TextType::class)
            ->add('password', TextType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setAllowedTypes('user_type', UserEnum::class);
        $resolver->setRequired('user_type');
//        $resolver->setDefaults([
//            'data_class' =>
//        ])
    }
}