<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class UserImageType extends AbstractType
{
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $user = $form->getParent()->getData();
        $view->vars['image_url'] = $user->getPicture();
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'label' => false,
            'compound' => true,
            'mapped' => false,
        ]);
    }

    public function getParent()
    {
        return TextType::class;
    }
}
