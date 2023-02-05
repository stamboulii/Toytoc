<?php

namespace App\Controller\Backoffice;



use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
//    private UserRepository $UserRepository;
//
//    public function __construct(UserRepository $UserRepository)
//    {
//        $this->UserRepository = $UserRepository ;
//    }
    #[Route('/index', name: 'app_backoffice_index')]
    public function index(): Response
    {
        return $this->render('backoffice/home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

//    public function configureMenuItems(): iterable
//    {
//        yield MenuItem::linkToDashboard('Dashboard');
//        yield MenuItem::linkToCrud('Users',  User::class);
//        yield MenuItem::linkToUrl('Homepage', $this->generateUrl('app_backoffice_index'));
//
//    }
//
//    public function configureActions(): Actions
//    {
//        return parent::configureActions()
//            ->add(Crud::PAGE_INDEX, Action::DETAIL);
//    }
//
//    public function configureUserMenu(UserInterface $user): UserMenu
//    {
//        return parent::configureUserMenu($user);
//    }


}
