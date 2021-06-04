<?php

namespace App\Form\AdminArticleEdit;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;

class FormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $option)
    {
        return $builder
            ->add('id', HiddenType::class)
            ->add('title', TextType::class)
            ->add('path', TextType::class)
            ->add('summary', TextareaType::class)
            ->add('body', TextareaType::class)
            ->add('commit', TextareaType::class)
            ->add('submit', SubmitType::class);
    }
}