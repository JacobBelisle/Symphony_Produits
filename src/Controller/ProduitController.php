<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Session\Session;

class ProduitController extends AbstractController
{
    private function produitsManager(ManagerRegistry $doctrine): ProduitRepository
    {
        return $doctrine->getManager()->getRepository(Produit::class);
    }


    #[Route('/produit/{id}', name: 'produit')]
    public function index(ManagerRegistry $doctrine, $id): Response
    {
        $product = $this->produitsManager($doctrine)->find($id);

        return $this->render('produit/index.html.twig', [
            'controller_name' => 'ProduitController',
            'id' => $id,
            'product' => $product,
        ]);
    }

    #[Route('/produits', name: 'produits')]
    public function listProduits(ManagerRegistry $doctrine): Response
    {
        $listProduits = $this->produitsManager($doctrine)->findAll();

        return $this->render('produit/listProduits.html.twig', [
            'list_produits' => $listProduits,
        ]);
    }

    #[Route('/ajoutProduit', name: 'ajoutProduit')]
    #[Security('is_granted(\'ROLE_ADMIN\')')]
    public function addProduit(Request $request, ManagerRegistry $doctrine)
    {
        $produit = new Produit();

        $form = $this->createForm(ProduitType::class, $produit);

        $form->add('ajouter', SubmitType::class, ['label' => 'Ajouter']);

        $form->handleRequest($request);     //ça doit être avant le isSubmitted()

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $this->produitsManager($doctrine)->add($produit);
                $session = new Session();
                $session->getFlashBag()->add('message', 'Le produit #' . $produit->getId() . ' a bien été ajouté');
                return $this->redirectToRoute('produits');
            }
        }

        return $this->renderForm('produit/produitForm.html.twig', [
            'action' => 'Ajouter',
            'form' => $form
        ]);
    }

    #[Route('/updateProduit/{id}', name: 'updateProduit')]
    #[Security('is_granted(\'ROLE_ADMIN\')')]
    public function updateProduit(Request $request, ManagerRegistry $doctrine, $id)
    {
        $produit = $this->produitsManager($doctrine)->find($id);

        $form = $this->createForm(ProduitType::class, $produit);

        $form->add('modifier', SubmitType::class, ['label' => 'Modifier']);

        $form->handleRequest($request);     //ça doit être avant le isSubmitted()

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $data = $form->getData();

                $produit = $data;

                $doctrine->getManager()->flush();

                $session = new Session();
                $session->getFlashBag()->add('message', 'Le produit #' . $id . ' a bien été modifié');

                return $this->redirectToRoute('produits');
            }
        }

        return $this->renderForm('produit/produitForm.html.twig', [
            'action' => 'Modifier',
            'form' => $form
        ]);
    }

    #[Route('/deleteProduit/{id}', name: 'deleteProduit')]
    #[Security('is_granted(\'ROLE_ADMIN\')')]
    public function deleteProduit(ManagerRegistry $doctrine, $id)
    {
        $produit = $this->produitsManager($doctrine)->find($id);

        $doctrine->getManager()->remove($produit);
        $doctrine->getManager()->flush();

        $session = new Session();
        $session->getFlashBag()->add('message', 'Le produit #' . $id . ' a bien été supprimé');

        return $this->redirectToRoute('produits');
    }
}
