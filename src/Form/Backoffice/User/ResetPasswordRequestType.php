<?php

namespace App\Form\Backoffice\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ResetPasswordRequestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('email', TextType::class, [
            'required' => true,
            'label' => 'Email',
            'constraints' => [
                new NotBlank(),
                new Email(message: 'Ce n\'est pas une adresse email valide')
            ]
        ]);
    }
}
