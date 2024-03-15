<?php

namespace App\Form;



use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RechercheType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('search', TextType::class, [
                'label' => 'Le nom de la sortie contient',
                'required' => false,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('organizator', CheckboxType::class, [
                'label' => 'Sorties que j\'organise',
                'required' => false,
                'attr' => [
                    'class' => 'form-check-input'
                ]
            ])
            ->add('participants', CheckboxType::class, [
                'label' => 'Sorties auxquelles je suis inscrit/e',
                'required' => false,
                'attr' => [
                    'class' => 'form-check-input'
                ]
            ])
            ->add('non_participants', CheckboxType::class, [
                'label' => 'Sorties auxquelles je suis PAS inscrit/e',
                'required' => false,
                'attr' => [
                    'class' => 'form-check-input'
                ]
            ])
            ->add('etat', CheckboxType::class, [
                'label' => 'Sorties passées',
                'required' => false,
                'attr' => [
                    'class' => 'form-check-input'
                ]
            ])
            ->add('dateMin', DateType::class, [
                'label' => 'Date min',
                'widget' => 'single_text',
                'required' => false,
                'row_attr' => [
                    'class' => 'datepicker'
                ]
            ])
            ->add('dateMax', DateType::class, [
                'label' => 'Date max',
                'widget' => 'single_text',
                'required' => false,
                'row_attr' => [
                    'class' => 'datepicker'
                ]
            ])
        ->add('submit', SubmitType::class, [
            'attr' => [
                'class' => 'btn btn-outline-success'
            ]
        ]);
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
        ]);
    }
}