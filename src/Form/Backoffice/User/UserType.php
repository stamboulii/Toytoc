<?php

namespace App\Form\Backoffice\User;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{

        public function buildForm(FormBuilderInterface $builder, array $options)
        {
            $builder
                ->add('firstname')
                ->add('lastname')
                ->add('email')
                ->add('password')
            ;
        }

        public function configureOptions(OptionsResolver $resolver)
        {
            $resolver->setDefaults([
                'data_class' => User::class,
            ]);
        }

}