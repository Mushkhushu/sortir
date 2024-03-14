<?php

namespace App\Form;

use App\Entity\Sorties;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatableMessage;

class SortiesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('date', null, [
                'widget' => 'single_text',
            ])
            ->add('duree')
            ->add('nbrPersonne')
            ->add('note', TextareaType::class)
            ->add('dateLimite', null, [
                'widget' => 'single_text',
            ])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-outline-success'
                ]
            ])
            ->add('participants', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'username',
                'multiple' => true,
                'expanded' => true,
            ])
        ;

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sorties::class,
        ]);
    }

    public function estExpiree(): bool
    {


        $date = new DateTime();
        $date->modify('+1 month');
        return true;


    }
}
