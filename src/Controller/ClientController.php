<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ClientController extends AbstractController
{
    #[Route('/contact', name: 'contact')]
    public function index(Request $request): Response
    {
        $form = $this->createFormBuilder()
            ->setAction($this->generateUrl('confirmationMessage'))
            ->setMethod('POST')
            ->add('name', TextType::class)
            ->add('prenom', TextType::class)
            ->add('courriel', EmailType::class)
            ->add(
                'typedemessage',
                ChoiceType::class,
                [
                    'label' => 'Type de message',
                    'choices' => [
                        'Question' => 'question',
                        'Commentaire' => 'commentaire',
                    ],
                    'expanded' => true
                ]
            )
            ->add('message', TextareaType::class, ['label' => 'Question/Commentaire'])
            ->add('envoyer', SubmitType::class, ['label' => 'Envoyer'])
            ->getForm();


        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isSubmitted()) {
                $answer = $form->getData();

                return $this->redirectToRoute('confirmationMessage', [
                    'answer' => $answer,
                ]);
            }
        }


        return $this->renderForm('client/index.html.twig', [
            'form' => $form,
        ]);
    }


    //Je sais pas comment faire en sorte que seulement
    //l'appel à partir de la méthode POST fonctionne
    #[Route('/confirmationMessage', name: 'confirmationMessage')]
    public function confirmationMessage(Request $request)
    {
        $ans = $request->request->all('form');
        return $this->render('client/confirmation.html.twig', [
            'ans' => $ans,
        ]);
    }
}
