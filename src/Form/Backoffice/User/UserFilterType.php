<?php

namespace App\Form\Backoffice\User;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;



class UserFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
            $builder
                ->add('q', TextType::class, [
                    'attr' => [
                        'placeholder' => 'Search..'
                    ]
                ]);

    }
    public function configureOptions(OptionsResolver $resolver)
    {
      $resolver->setDefaults([
            'data_class' => SearchUsers::class,
            'method' => 'GET',
            'csrf_protection' => false,
           ]);
    }


}
