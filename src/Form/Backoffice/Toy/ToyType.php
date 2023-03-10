<?php

namespace App\Form\Backoffice\Toy;

use App\Entity\Toy;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\QueryBuilder;

class ToyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('category', EntityType::class, [
                'class'         => Category::class,
                'query_builder' => function (CategoryRepository $repository): QueryBuilder {
                    return $repository->createQueryBuilder('e')
                                      ->orderBy('e.name');
                }
            ])
            ->add('user', EntityType::class, [
                'class'         => User::class,
                'query_builder' => function (UserRepository $repository): QueryBuilder {
                    return $repository->createQueryBuilder('e')
                                      ->where('e.roles LIKE :roles')
                                      ->setParameter('roles', '%' . User::ROLE_PARENT . '%')
                                      ->orderBy('e.firstName');
                }
            ])
            ->add('weight', TextType::class)
            ->add('price', TextType::class)
            ->add('state', TextType::class)
            ->add('images', FileType::class, [
                'label' => 'PDF file',
                'attr' => ['placeholder' => 'Choose file'],
                'required' => false,
                'multiple' => true,
                'mapped'   => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Toy::class,
        ]);
    }
}
