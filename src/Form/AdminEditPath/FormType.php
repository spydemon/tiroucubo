<?php

namespace App\Form\AdminEditPath;

use App\Entity\Path;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class FormType extends AbstractType
{
    private TranslatorInterface $translator;

    public function __construct(
        TranslatorInterface $translator
    ) {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $editionMode = $options['edition_mode'];
        return $builder
            ->add('id', HiddenType::class)
            ->add('slug', TextType::class, ['disabled' => $editionMode ? 'disabled' : ''])
            ->add('title', TextType::class)
            ->add('custom_template', TextType::class, ['required' => false])
            ->add('type', ChoiceType::class, ['choices' => $this->getTypeChoices()])
            ->add('submit', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        // The form can be used for creating a new path entity or for updating an already existing one. This option
        // should be set at "true" if we are editing an already existing Path.
        $resolver->setDefaults(['edition_mode' => false]);
        $resolver->setAllowedTypes('edition_mode', 'bool');
        parent::configureOptions($resolver);
    }

    protected function getTypeChoices() : array
    {
        return [
            $this->translator->trans('Dynamic') => Path::TYPE_DYNAMIC,
            $this->translator->trans('Always visible') => Path::TYPE_ALWAYS_VISIBLE
        ];
    }
}