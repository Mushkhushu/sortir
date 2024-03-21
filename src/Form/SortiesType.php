<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Sorties;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
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
        $displayParticipants = $options['display_participants'] ?? true; // Par défaut, afficher le champ 'participants'

        if ($displayParticipants) {
            $builder->add('participants', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'username',
                'multiple' => true,
                'expanded' => true,
            ]);
        }
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
                    'class' => 'btn btn-success'
                ]

            ])
            ->add('lieu', EntityType::class, [
                'class' => Lieu::class,
                'choice_label' => 'nom',
            ]);





    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sorties::class,
            'display_participants' => true, // Par défaut, affichez le champ 'participants'
        ]);
    }

}
