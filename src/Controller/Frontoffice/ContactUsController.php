<?php

namespace App\Controller\Frontoffice;

use App\Entity\ContactUs;
use App\Form\Frontoffice\Contact\ContactUsType;
use App\Repository\ContactUsRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/contact')]
class ContactUsController extends AbstractController
{
    #[Route('/', name: 'app_frontoffice_contact', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function index(Request $request, ContactUsRepository $ContactRepository): Response
    {
        $form = $this->createForm(ContactUsType::class, $contact = new ContactUs());
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $ContactRepository->save($contact, true);

            return $this->redirectToRoute('app_frontoffice_contact', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('frontoffice/contact/contact.html.twig', [
            'contact' => $contact,
            'form'    => $form,
        ]);
    }


    #[Route('/delete/{id}', name: 'app_frontoffice_contact_delete', methods: ['POST'])]
    public function delete(Request $request, ContactUs $contact, ContactUsRepository $contactUsRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $contact->getId(), $request->request->get('_token'))) {
            $contactUsRepository->remove($contact, true);
        }

        return $this->redirectToRoute('contactUs_delete', [], Response::HTTP_SEE_OTHER);
    }
}
