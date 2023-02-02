<?php

namespace App\Form\Backoffice\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class ResetPasswordResetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('password', RepeatedType::class, [
            'type'            => PasswordType::class,
            'invalid_message' => 'The password fields must match.',
            'options'         => ['attr' => ['class' => 'password-field']],
            'required'        => true,
            'first_options'   => ['label' => 'Password'],
            'second_options'  => ['label' => 'Repeat Password'],
        ]);
    }
}
