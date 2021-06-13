<?php

namespace App\Form\AdminMediaEdit;

use App\Entity\Media;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;

class FormType extends Abstracttype
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        return $builder
            ->add('id', HiddenType::class)
            ->add('path',CollectionType::class, [
                'allow_add' => true,
                'allow_delete' => true,
                'entry_type' => TextType::class,
                'label' => 'Path',
                'prototype' => true,
            ])
            ->add('media', FileType::class, [
                'constraints' => [
                    new File([
                        'maxSize' => '10m',
                        'mimeTypes' => Media::getAllowedResourceType(),
                        'mimeTypesMessage' =>
                            'Content type is not allowed. Allowed ones are: '
                            . implode(', ', Media::getAllowedResourceType()),
                    ])
                ]
            ])
            ->add('submit', SubmitType::class);
    }
}